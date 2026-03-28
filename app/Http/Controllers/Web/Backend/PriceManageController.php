<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\PriceManage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;

class PriceManageController extends Controller
{
    /**
     * Display a listing of all users.
     *
     * @param Request $request
     * @return JsonResponse|View
     */
    public function index(Request $request): JsonResponse | View {
        if ($request->ajax()) {
            $data = PriceManage::latest()->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    return '<div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
                                <a href="' . route('price-manage.edit', ['id' => $data->id]) . '" type="button" class="btn btn-primary fs-14 text-white edit-icn" title="Edit">
                                    <i class="fe fe-edit"></i>
                                </a>
                            </div>';
                })
                ->rawColumns(['action'])
                ->make();
        }
        return view('backend.layouts.price-manage.index');
    }

    public function edit(int $id): View {
        $data = PriceManage::findOrFail($id);
        return view('backend.layouts.price-manage.edit', compact('data'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        try {

            $data = PriceManage::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'service_fee'          => 'sometimes|string|numeric|min:0',
                'pickup_fee'           => 'sometimes|string|numeric|min:0',
                'weight_per_kg_price'  => 'sometimes|string|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $data->service_fee          = $request->service_fee ?? $data->service_fee;
            $data->pickup_fee           = $request->pickup_fee ?? $data->pickup_fee;
            $data->weight_per_kg_price  = $request->weight_per_kg_price ?? $data->weight_per_kg_price;
            $data->update();

            return redirect()->route('price-manage.index')->with('t-success', 'Data Updated Successfully.');

        } catch (\Exception $exception) {
            return redirect()->route('price-manage.index')->with('t-success', 'Data failed to update');
        }
    }

}
