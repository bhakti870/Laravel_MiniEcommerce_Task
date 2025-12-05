<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Http\Requests\StoreProductVariantRequest;
use App\Http\Requests\UpdateProductVariantRequest;

class ProductVariantController extends Controller
{
    public function index(Product $product): View
    {
        $variants = $product->variants()->latest()->get();
        return view('variants.index', compact('product', 'variants'));
    }

    public function create(Product $product): View
    {
        return view('variants.create', compact('product'));
    }

    public function store(StoreProductVariantRequest $request, Product $product): RedirectResponse
    {
        $product->variants()->create($request->validated());

        return redirect()->route('products.show', $product->id)
            ->with('success', 'Variant added successfully');
    }

    public function edit(Product $product, ProductVariant $variant): View
    {
        return view('variants.edit', compact('product', 'variant'));
    }

    public function update(UpdateProductVariantRequest $request, Product $product, ProductVariant $variant): RedirectResponse
    {
        $variant->update($request->validated());

        return redirect()->route('products.show', $product->id)
            ->with('success', 'Variant updated successfully');
    }

    public function destroy(Product $product, ProductVariant $variant): RedirectResponse
    {
        $variant->delete();
        return redirect()->route('products.show', $product->id)
            ->with('success', 'Variant deleted successfully');
    }
}
