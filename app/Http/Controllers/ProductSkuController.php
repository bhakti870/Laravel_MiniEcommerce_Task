<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductSku;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ProductSkuController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ProductSku::with('product')->latest()->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('product', fn($row) => $row->product->name ?? '-')
                ->addColumn('action', function($row){
                    return '
                        <button class="btn btn-sm btn-primary editBtn" data-id="'.$row->id.'">Edit</button>
                        <button class="btn btn-sm btn-danger deleteBtn" data-id="'.$row->id.'">Delete</button>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('skus.index');
    }

    public function productSkus($productId)
    {
        return ProductSku::where('product_id', $productId)->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required',
            'sku'        => 'required',
            'price'      => 'required',
            'stock'      => 'required',
            'size'       => 'nullable',
            'color'      => 'nullable'
        ]);

        ProductSku::updateOrCreate(
            ['id' => $request->id],
            $data
        );

        return response()->json(['success' => 'SKU saved successfully']);
    }

    public function edit($id)
    {
        return ProductSku::findOrFail($id);
    }

    public function destroy($id)
    {
        ProductSku::findOrFail($id)->delete();
        return response()->json(['success' => 'SKU deleted']);
    }
}
