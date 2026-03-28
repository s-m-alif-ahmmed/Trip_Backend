<?php

namespace App\Http\Controllers\Web\Backend;

use App\Enums\TripBookingStatus;
use App\Http\Controllers\Controller;
use App\Models\TripBooking;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;

class TripBookingController extends Controller
{
    /**
     * Display a listing of all trip bookings.
     *
     * @param Request $request
     * @return JsonResponse|View
     */
    public function index(Request $request): JsonResponse | View {
        if ($request->ajax()) {
            $data = TripBooking::with('trip')->latest()->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('pickup_address', function (TripBooking $booking) {
                    return $booking->pickup_address ?? 'N/A';
                })
                ->addColumn('pickup_service_status', function (TripBooking $booking) {
                    return $booking->pickup_service_status 
                        ? '<span class="badge bg-info">Requested</span>' 
                        : '<span class="badge bg-secondary">No</span>';
                })
                ->addColumn('trip_info', function (TripBooking $booking) {
                    if (!$booking->trip) return 'N/A';
                    return sprintf(
                        '%s -> %s (%s)',
                        $booking->trip->departure_city,
                        $booking->trip->arrival_city,
                        $booking->trip->date->format('Y-m-d')
                    );
                })
                ->addColumn('total_amount', function (TripBooking $booking) {
                    return '€' . number_format($booking->total, 2);
                })
                ->addColumn('payment_status', function (TripBooking $booking) {
                    return $booking->is_paid
                        ? '<span class="badge bg-success">Paid</span>'
                        : '<span class="badge bg-danger">Unpaid</span>';
                })
                ->addColumn('status', function (TripBooking $booking) {
                    // Build options from TripBookingStatus enum
                    $statusOptions = [
                        TripBookingStatus::PENDING->value   => TripBookingStatus::PENDING->label(),
                        TripBookingStatus::COMPLETED->value => TripBookingStatus::COMPLETED->label(),
                        TripBookingStatus::CANCELLED->value => TripBookingStatus::CANCELLED->label(),
                    ];

                    $currentStatus = $booking->status instanceof TripBookingStatus
                        ? $booking->status->value
                        : $booking->status;

                    $html = '<select class="form-select status-dropdown"
                                     data-id="' . $booking->id . '"
                                     onchange="showStatusChangeAlert(' . $booking->id . ', this.value)"
                                     style="width: 130px;">';

                    foreach ($statusOptions as $value => $label) {
                        $selected = $currentStatus === $value ? 'selected' : '';
                        $html .= '<option value="' . $value . '" ' . $selected . '>' . $label . '</option>';
                    }

                    $html .= '</select>';

                    return $html;
                })
                ->addColumn('action', function (TripBooking $booking) {
                    $viewUrl = route('trip-booking.show', ['id' => $booking->id]);

                    return '<div class="btn-group btn-group-sm" role="group" aria-label="Actions">
                                <a href="' . $viewUrl . '" type="button" class="btn btn-primary fs-14 text-white me-1" title="View">
                                    <i class="fe fe-eye"></i>
                                </a>
                                <button type="button" onclick="showDeleteConfirm(' . $booking->id . ')" class="btn btn-danger fs-14 text-white delete-icn" title="Delete">
                                    <i class="fe fe-trash"></i>
                                </button>
                            </div>';
                })
                ->rawColumns(['pickup_service_status', 'payment_status', 'status', 'action'])
                ->make();
        }
        return view('backend.layouts.trip-booking.index');
    }

    /**
     * Display a listing of all trip bookings.
     *
     * @param Request $request
     * @return JsonResponse|View
     */
    public function pickupIndex(Request $request): JsonResponse | View {
        if ($request->ajax()) {
            $data = TripBooking::with('trip')->where('pickup_service_status', true)->latest()->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('full_name', function (TripBooking $booking) {
                    return $booking->full_name ?? 'N/A';
                })
                ->addColumn('email', function (TripBooking $booking) {
                    return $booking->email ?? 'N/A';
                })
                ->addColumn('phone_number', function (TripBooking $booking) {
                    return $booking->phone_number ?? 'N/A';
                })
                ->addColumn('address', function (TripBooking $booking) {
                    return $booking->address ?? 'N/A';
                })
                ->addColumn('weight', function (TripBooking $booking) {
                    return $booking->weight ? $booking->weight . ' Kg' : 'N/A';
                })
                ->addColumn('departure', function (TripBooking $booking) {
                    return $booking->trip ? $booking->trip->departure_city : 'N/A';
                })
                ->addColumn('destination', function (TripBooking $booking) {
                    return $booking->trip ? $booking->trip->arrival_city : 'N/A';
                })
                ->addColumn('date', function (TripBooking $booking) {
                    return $booking->date ? Carbon::parse($booking->date)->format('Y-m-d') : 'N/A';
                })
                ->addColumn('time', function (TripBooking $booking) {
                    return $booking->time ? Carbon::parse($booking->time)->format('H:i') : 'N/A';
                })
                ->addColumn('status', function (TripBooking $booking) {
                    // Build options from TripBookingStatus enum
                    $statusOptions = [
                        TripBookingStatus::PENDING->value   => TripBookingStatus::PENDING->label(),
                        TripBookingStatus::COMPLETED->value => TripBookingStatus::COMPLETED->label(),
                        TripBookingStatus::CANCELLED->value => TripBookingStatus::CANCELLED->label(),
                    ];

                    $currentStatus = $booking->status instanceof TripBookingStatus
                        ? $booking->status->value
                        : $booking->status;

                    $html = '<select class="form-select status-dropdown"
                                     data-id="' . $booking->id . '"
                                     onchange="showStatusChangeAlert(' . $booking->id . ', this.value)"
                                     style="width: 130px;">';

                    foreach ($statusOptions as $value => $label) {
                        $selected = $currentStatus === $value ? 'selected' : '';
                        $html .= '<option value="' . $value . '" ' . $selected . '>' . $label . '</option>';
                    }

                    $html .= '</select>';

                    return $html;
                })
                ->addColumn('action', function (TripBooking $booking) {
                    $viewUrl = route('trip-booking.show', ['id' => $booking->id]);

                    return '<div class="btn-group btn-group-sm" role="group" aria-label="Actions">
                                <a href="' . $viewUrl . '" type="button" class="btn btn-primary fs-14 text-white me-1" title="View">
                                    <i class="fe fe-eye"></i>
                                </a>
                                <button type="button" onclick="showDeleteConfirm(' . $booking->id . ')" class="btn btn-danger fs-14 text-white delete-icn" title="Delete">
                                    <i class="fe fe-trash"></i>
                                </button>
                            </div>';
                })
                ->rawColumns(['full_name', 'status', 'action'])
                ->make();
        }
        return view('backend.layouts.trip-booking.pickup-index');
    }

    /**
     * Display the specified booking.
     *
     * @param int $id
     * @return View
     */
    public function show(int $id): View
    {
        $booking = TripBooking::with('trip.user')->findOrFail($id);

        return view('backend.layouts.trip-booking.show', compact('booking'));
    }

    /**
     * Update the status of the specified booking.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function status(Request $request, int $id): JsonResponse
    {
        try {
            $statusValue = $request->input('status');

            // Validate incoming status against TripBookingStatus enum
            $statusEnum = TripBookingStatus::tryFrom($statusValue);
            if (! $statusEnum) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid status value.',
                ], 422);
            }

            $booking = TripBooking::findOrFail($id);
            $booking->status = $statusEnum;
            $booking->save();

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

    /**
     * Remove the specified booking from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $booking = TripBooking::findOrFail($id);
            $booking->delete();

            return response()->json([
                't-success' => true,
                'message'   => 'Deleted successfully.',
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                't-success' => false,
                'message'   => $exception->getMessage(),
            ], 500);
        }
    }
}
