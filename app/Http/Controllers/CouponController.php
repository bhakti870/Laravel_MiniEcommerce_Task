<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use DataTables;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Coupon::latest()->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $btn = '<a href="javascript:void(0)" data-id="'.$row->id.'" class="editBtn btn btn-sm btn-primary">Edit</a> ';
                    $btn .= '<a href="javascript:void(0)" data-id="'.$row->id.'" class="deleteBtn btn btn-sm btn-danger">Delete</a>';
                    return $btn;
                })
                ->addColumn('status', function($row){
                    $checked = $row->is_active ? 'checked' : '';
                    return '<input type="checkbox" class="changeStatus" data-id="'.$row->id.'" '.$checked.'>';
                })
                ->rawColumns(['action','status'])
                ->make(true);
        }

        return view('coupons.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:coupons,code,'.$request->id,
            'type' => 'required|in:fixed,percent',
            'value' => 'required|numeric|min:0',
            'max_uses' => 'nullable|numeric|min:0',
            'min_cart_value' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $coupon = Coupon::updateOrCreate(
            ['id' => $request->id],
            $request->only('code','type','value','max_uses','min_cart_value','start_date','end_date','is_active')
        );

        return response()->json(['success'=>'Coupon saved successfully.']);
    }

    public function edit($id)
    {
        $coupon = Coupon::findOrFail($id);
        return response()->json($coupon);
    }

    public function destroy($id)
    {
        Coupon::findOrFail($id)->delete();
        return response()->json(['success'=>'Coupon deleted successfully.']);
    }

    public function changeStatus(Request $request)
    {
        $coupon = Coupon::findOrFail($request->id);
        $coupon->is_active = $request->status;
        $coupon->save();
        return response()->json(['message'=>'Status updated successfully']);
    }
}
