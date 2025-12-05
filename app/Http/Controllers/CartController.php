<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:cart-list|cart-create|cart-edit|cart-delete', ['only' => ['index','show']]);
        $this->middleware('permission:cart-create', ['only' => ['create','store']]);
        $this->middleware('permission:cart-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:cart-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $rows = Cart::with('user')->latest()->get();

            return DataTables::of($rows)
                ->addIndexColumn()
                ->addColumn('user', function ($row) {
                    return $row->user->name ?? 'Guest';
                })
                ->addColumn('subtotal', function ($row) {
                    return number_format($row->subtotal, 2);
                })
                ->addColumn('discount', function ($row) {
                    return number_format($row->discount, 2);
                })
                ->addColumn('tax', function ($row) {
                    return number_format($row->tax, 2);
                })
                ->addColumn('total', function ($row) {
                    return number_format($row->total, 2);
                })
                ->addColumn('action', function ($row) {
                    return '
                        <a href="'.route('carts.items', $row->id).'" class="btn btn-sm btn-secondary">Items</a>
                        <button class="btn btn-sm btn-primary editBtn" data-id="'.$row->id.'">Edit</button>
                        <button class="btn btn-sm btn-danger deleteBtn" data-id="'.$row->id.'">Delete</button>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('carts.index');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id' => 'nullable|exists:carts,id',
            'user_id' => 'nullable|exists:users,id',
            'subtotal' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'tax' => 'nullable|numeric',
            'total' => 'required|numeric',
            'session_id' => 'nullable|string'
        ]);

        Cart::updateOrCreate(['id' => $request->id], $data);

        return response()->json(['success' => 'Cart saved successfully.']);
    }

    public function edit($id)
    {
        $cart = Cart::findOrFail($id);
        return response()->json($cart);
    }

    public function destroy($id)
    {
        Cart::findOrFail($id)->delete();
        return response()->json(['success' => 'Cart deleted successfully.']);
    }
}
