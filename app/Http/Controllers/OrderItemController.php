<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductSku;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class OrderItemController extends Controller
{
    public function index($orderId, Request $request)
    {
        $order = Order::findOrFail($orderId);

        if ($request->ajax()) {
            $data = OrderItem::with('productSku.product')
                ->where('order_id', $orderId)
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('sku', fn($row) => $row->productSku->sku ?? '-')
                ->addColumn('product', fn($row) => $row->productSku->product->name ?? '-')
                ->addColumn('action', function ($row) {
                    return '
                    <button class="btn btn-sm btn-primary editItemBtn" data-id="'.$row->id.'">Edit</button>
                    <button class="btn btn-sm btn-danger deleteItemBtn" data-id="'.$row->id.'">Delete</button>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('orders.items', compact('order'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id' => 'nullable|exists:order_items,id',
            'order_id' => 'required|exists:orders,id',
            'product_sku_id' => 'required|exists:product_skus,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric',
            'discount' => 'nullable|numeric',
        ]);

        $total = ($request->price * $request->quantity) - ($request->discount ?? 0);

        OrderItem::updateOrCreate(
            ['id' => $request->id],
            [
                'order_id' => $request->order_id,
                'product_sku_id' => $request->product_sku_id,
                'quantity' => $request->quantity,
                'price' => $request->price,
                'discount' => $request->discount ?? 0,
                'total' => $total,
            ]
        );

        return response()->json(['success' => 'Order item saved successfully']);
    }

    public function edit($id)
    {
        return response()->json(OrderItem::findOrFail($id));
    }

    public function destroy($id)
    {
        OrderItem::findOrFail($id)->delete();
        return response()->json(['success' => 'Order item deleted']);
    }
}
