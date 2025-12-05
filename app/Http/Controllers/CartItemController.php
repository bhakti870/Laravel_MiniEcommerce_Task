<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductSku;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CartItemController extends Controller
{
    public function index($cartId, Request $request)
    {
        $cart = Cart::findOrFail($cartId);

        if ($request->ajax()) {
            $items = CartItem::with(['product', 'sku'])->where('cart_id', $cartId)->get();

            return DataTables::of($items)
                ->addIndexColumn()
                ->addColumn('sku', function ($row) {
                    return optional($row->sku)->sku ?? '-';
                })
                ->addColumn('product', function ($row) {
                    return optional($row->product)->name ?? '-';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <button class="btn btn-sm btn-primary editItemBtn" data-id="'.$row->id.'">Edit</button>
                        <button class="btn btn-sm btn-danger deleteItemBtn" data-id="'.$row->id.'">Delete</button>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('carts.items', compact('cart'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id' => 'nullable|exists:cart_items,id',
            'cart_id' => 'required|exists:carts,id',
            'product_id' => 'required|exists:products,id',
            'product_sku_id' => 'nullable|exists:product_skus,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric',
            'discount' => 'nullable|numeric'
        ]);

        $total = ($data['price'] * $data['quantity']) - ($data['discount'] ?? 0);

        CartItem::updateOrCreate(
            ['id' => $request->id],
            [
                'cart_id' => $data['cart_id'],
                'product_id' => $data['product_id'],
                'product_sku_id' => $data['product_sku_id'] ?? null,
                'quantity' => $data['quantity'],
                'price' => $data['price'],
                'discount' => $data['discount'] ?? 0,
                'total' => $total,
            ]
        );

        // Optionally update cart totals here (simple recalculation)
        $this->recalculateCartTotals($data['cart_id']);

        return response()->json(['success' => 'Cart item saved successfully.']);
    }

    public function edit($id)
    {
        return response()->json(CartItem::findOrFail($id));
    }

    public function destroy($id)
    {
        $item = CartItem::findOrFail($id);
        $cartId = $item->cart_id;
        $item->delete();

        // Recalculate totals
        $this->recalculateCartTotals($cartId);

        return response()->json(['success' => 'Cart item deleted successfully.']);
    }

    protected function recalculateCartTotals($cartId)
    {
        $cart = Cart::findOrFail($cartId);
        $items = $cart->items()->get();

        $subtotal = $items->sum(function ($i) { return $i->price * $i->quantity; });
        $discount = $items->sum('discount');
        $tax = 0; // keep tax calculation optional/custom
        $total = $subtotal - $discount + $tax;

        $cart->update([
            'subtotal' => $subtotal,
            'discount' => $discount,
            'tax' => $tax,
            'total' => $total,
        ]);
    }
}
