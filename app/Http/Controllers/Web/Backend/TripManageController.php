<?php

namespace App\Http\Controllers\Web\Backend;

use App\Enums\TripStatus;
use App\Http\Controllers\Controller;
use App\Models\Trip;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;

class TripManageController extends Controller
{
    /**
     * Display a listing of all users.
     *
     * @param Request $request
     * @return JsonResponse|View
     */
    public function index(Request $request): JsonResponse | View {
        if ($request->ajax()) {
            $data = Trip::with('user')->latest()->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('user_name', function (Trip $trip) {
                    return optional($trip->user)->name ?? '-';
                })
                ->addColumn('from', function (Trip $trip) {
                    return sprintf(
                        '%s, %s (%s)',
                        $trip->departure_city,
                        $trip->departure_country,
                        $trip->departure_country_code
                    );
                })
                ->addColumn('to', function (Trip $trip) {
                    return sprintf(
                        '%s, %s (%s)',
                        $trip->arrival_city,
                        $trip->arrival_country,
                        $trip->arrival_country_code
                    );
                })
                ->addColumn('date', function (Trip $trip) {
                    return optional($trip->date)->format('Y-m-d');
                })
                ->addColumn('time', function (Trip $trip) {
                    return optional($trip->time)->format('H:i');
                })

                ->addColumn('available_weight', function (Trip $trip) {
                    return $trip->available_weight . ' kg';
                })
                ->addColumn('status', function (Trip $trip) {
                    // Build options from TripStatus enum
                    $statusOptions = [
                        TripStatus::PENDING->value      => TripStatus::PENDING->label(),
                        TripStatus::ACTIVE->value       => TripStatus::ACTIVE->label(),
                        TripStatus::DEACTIVATED->value  => TripStatus::DEACTIVATED->label(),
                    ];

                    // Determine current status value as string
                    $currentStatus = null;
                    if ($trip->status instanceof TripStatus) {
                        $currentStatus = $trip->status->value;
                    } elseif (! is_null($trip->status)) {
                        $currentStatus = TripStatus::tryFrom($trip->status)?->value;
                    }

                    if ($currentStatus === null) {
                        $currentStatus = TripStatus::PENDING->value;
                    }

                    $html = '<select class="form-select status-dropdown"
                                     data-id="' . $trip->id . '"
                                     onchange="showStatusChangeAlert(' . $trip->id . ', this.value)"
                                     style="width: 130px;">';

                    foreach ($statusOptions as $value => $label) {
                        $selected = $currentStatus === $value ? 'selected' : '';
                        $html .= '<option value="' . $value . '" ' . $selected . '>' . $label . '</option>';
                    }

                    $html .= '</select>';

                    return $html;
                })
                ->addColumn('action', function (Trip $trip) {
                    $viewUrl = route('trip.show', ['id' => $trip->id]);
                    $editUrl = route('trip.edit', ['id' => $trip->id]);

                    $approveButton = '';
                    if ($trip->status === TripStatus::PENDING) {
                        $approveButton = '<button type="button" onclick="approveTrip(' . $trip->id . ')" class="btn btn-success fs-14 text-white me-1" title="Approve">
                                              <i class="fe fe-check"></i>
                                          </button>';
                    }

                    return '<div class="btn-group btn-group-sm" role="group" aria-label="Actions">
                                <a href="' . $viewUrl . '" type="button" class="btn btn-primary fs-14 text-white me-1" title="View">
                                    <i class="fe fe-eye"></i>
                                </a>
                                <a href="' . $editUrl . '" type="button" class="btn btn-info fs-14 text-white me-1" title="Edit">
                                    <i class="fe fe-edit"></i>
                                </a>'
                                . $approveButton .
                                '<button type="button" onclick="showDeleteConfirm(' . $trip->id . ')" class="btn btn-danger fs-14 text-white delete-icn" title="Delete">
                                    <i class="fe fe-trash"></i>
                                </button>
                            </div>';
                })
                ->rawColumns(['status', 'action'])
                ->make();
        }
        return view('backend.layouts.trip.index');
    }

    public function show(int $id): View
    {
        $trip = Trip::with('user')->findOrFail($id);

        return view('backend.layouts.trip.show', compact('trip'));
    }

    public function edit(int $id): View
    {
        $trip = Trip::with('user')->findOrFail($id);
        $statuses = TripStatus::cases();

        return view('backend.layouts.trip.edit', compact('trip', 'statuses'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $statusValues = array_map(fn (TripStatus $s) => $s->value, TripStatus::cases());

        $validated = $request->validate([
            'departure_city'          => ['required', 'string', 'max:255'],
            'departure_country'       => ['required', 'string', 'max:255'],
            'departure_country_code'  => ['required', 'string', 'max:10'],
            'arrival_city'            => ['required', 'string', 'max:255'],
            'arrival_country'         => ['required', 'string', 'max:255'],
            'arrival_country_code'    => ['required', 'string', 'max:10'],
            'available_weight'        => ['required', 'numeric', 'min:0'],
            'date'                    => ['required', 'date'],
            'time'                    => ['required', 'date_format:H:i'],
            'status'                  => ['required', 'string', 'in:' . implode(',', $statusValues)],
        ]);

        $trip = Trip::findOrFail($id);
        $trip->departure_city = $validated['departure_city'];
        $trip->departure_country = $validated['departure_country'];
        $trip->departure_country_code = $validated['departure_country_code'];
        $trip->arrival_city = $validated['arrival_city'];
        $trip->arrival_country = $validated['arrival_country'];
        $trip->arrival_country_code = $validated['arrival_country_code'];
        $trip->available_weight = $validated['available_weight'];
        $trip->date = $validated['date'];
        $trip->time = $validated['time'];
        $trip->status = TripStatus::from($validated['status']);
        $trip->save();

        return redirect()
            ->route('trip.index')
            ->with('t-success', 'Trip updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $trip = Trip::findOrFail($id);
        $trip->delete();

        return response()->json([
            't-success' => true,
            'message'   => 'Deleted successfully.',
        ]);
    }

    /**
     * Update the status of the specified trip.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function status(Request $request, int $id): JsonResponse
    {
        try {
            $statusValue = $request->input('status');

            // Validate incoming status against TripStatus enum
            $statusEnum = TripStatus::tryFrom($statusValue);
            if (! $statusEnum) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid status value.',
                ], 422);
            }

            $trip = Trip::findOrFail($id);
            $trip->status = $statusEnum;
            $trip->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

}
