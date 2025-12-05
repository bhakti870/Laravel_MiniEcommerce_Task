<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use PDF;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $orders = Order::with('user')->latest()->get();

            return DataTables::of($orders)
                ->addIndexColumn()
                ->addColumn('user', function ($row) {
                    return $row->user ? $row->user->name : 'N/A';
                })
                ->addColumn('status_dropdown', function ($row) {
                    $statuses = ['pending','confirmed','shipped','delivered','cancelled'];

                    $html = '<select class="form-select form-select-sm changeStatus" data-id="'.$row->id.'">';
                    foreach ($statuses as $st) {
                        $selected = $row->status === $st ? 'selected' : '';
                        $html .= '<option value="'.$st.'" '.$selected.'>'.ucfirst($st).'</option>';
                    }
                    $html .= '</select>';

                    return $html;
                })
                ->addColumn('action', function ($row) {
                    $showUrl = route('orders.show', $row->id);
                    $itemsUrl = route('order.items', $row->id);
                    $invoiceUrl = route('orders.invoice', $row->id);

                    return '
                        <a href="'.$showUrl.'" class="btn btn-sm btn-info">View</a>
                        <a href="'.$itemsUrl.'" class="btn btn-sm btn-secondary">Items</a>
                        <a href="'.$invoiceUrl.'" class="btn btn-sm btn-success" target="_blank">Invoice</a>
                        <button class="btn btn-sm btn-primary editBtn" data-id="'.$row->id.'">Edit</button>
                        <button class="btn btn-sm btn-danger deleteBtn" data-id="'.$row->id.'">Delete</button>
                    ';
                })
                ->rawColumns(['status_dropdown', 'action'])
                ->make(true);
        }

        return view('orders.index');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id'       => 'nullable|exists:orders,id',
            'user_id'  => 'required|exists:users,id',
            'subtotal' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'total'    => 'required|numeric',
            'status'   => 'required|string'
        ]);

        Order::updateOrCreate(['id' => $request->id], $data);

        return response()->json(['success' => 'Order saved successfully.']);
    }

    public function edit($id)
    {
        $order = Order::findOrFail($id);
        return response()->json($order);
    }

    public function destroy($id)
    {
        Order::findOrFail($id)->delete();
        return response()->json(['success' => 'Order deleted successfully.']);
    }

    /**
     * Show pretty JSON snapshot
     */
    public function show($id)
    {
        $order = Order::findOrFail($id);
        return view('orders.show', compact('order'));
    }

    /**
     * Change status via AJAX
     */
    public function changeStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:orders,id',
            'status' => 'required|string'
        ]);

        $order = Order::findOrFail($request->id);
        $order->status = $request->status;
        $order->save();

        return response()->json(['message' => 'Order status updated successfully']);
    }

    /**
     * Invoice PDF download
     */
    public function invoice($id)
    {
        $order = Order::with('items.productSku.product', 'user')->findOrFail($id);

        $pdf = PDF::loadView('orders.invoice', compact('order'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("invoice-order-{$order->id}.pdf");
    }
}
