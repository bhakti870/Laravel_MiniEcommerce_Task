<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:product-list|product-create|product-edit|product-delete', ['only' => ['index','show']]);
         $this->middleware('permission:product-create', ['only' => ['create','store']]);
         $this->middleware('permission:product-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:product-delete', ['only' => ['destroy']]);
    }
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $products = Product::with(['category', 'brand'])->select('products.*');

            return DataTables::of($products)
                ->addColumn('image', function ($row) {
                    return $row->image 
                        ? '<img src="'.asset('storage/'.$row->image).'" width="50" height="50">' 
                        : '-';
                })
                ->addColumn('category', function($row) {
                    return $row->category ? $row->category->name : '-';
                })
                ->addColumn('brand', function($row) {
                    return $row->brand ? $row->brand->name : '-';
                })
                ->addColumn('status', function ($row) {
                    return $row->status
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-danger">Inactive</span>';
                })
                ->addColumn('action', function($row) {
                    $btn = '<a href="javascript:void(0)" data-id="'.$row->id.'" class="showBtn btn btn-info btn-sm">Show</a>';
                    $btn .= ' <a href="'.route("products.edit",$row->id).'" class="edit btn btn-primary btn-sm">Edit</a>';
                    $btn .= ' <button class="btn btn-danger btn-sm deleteBtn" data-id="'.$row->id.'">Delete</button>';
                    return $btn;
                })
                ->rawColumns(['image', 'status', 'action'])
                ->make(true);
        }

        $categories = Category::where('status', 1)->get();
        $brands = Brand::where('status', 1)->get();
        return view('products.index', compact('categories', 'brands'));
    }

    // public function show(Product $product)
    // {
    //     $product->load(['category', 'brand', 'variants', 'images']);
    //     if (request()->ajax()) {
    //         return response()->json($product);
    //     }
    //     return redirect()->route('products.index');
    // }

    public function show($slug)
{
    $product = Product::where('slug', $slug)
        ->with(['category', 'brand', 'variants', 'images'])
        ->firstOrFail();

    if (request()->ajax()) {
        return response()->json($product);
    }

    return view('frontend.product_detail', compact('product'));
}




    public function create()
    {
        return redirect()->route('products.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'brand_id' => 'required',
            'sku' => 'nullable|unique:products,sku',
            'price' => 'required|numeric',
            'image' => 'nullable|image',
        ]);

        DB::transaction(function () use ($request) {
            $productData = $request->only(['name', 'detail', 'category_id', 'brand_id', 'sku', 'price', 'stock', 'status']);
            $productData['slug'] = Str::slug($request->name);

            if ($request->hasFile('image')) {
                $productData['image'] = $request->file('image')->store('products', 'public');
            }

            $product = Product::create($productData);

            // Variants
            if ($request->has('variants')) {
                foreach ($request->variants as $variant) {
                    if(isset($variant['sku']) && $variant['sku']) {
                         $product->variants()->create($variant);
                    }
                }
            }

            // Multiple Images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('products', 'public');
                    $product->images()->create(['image_path' => $path]);
                }
            }
        });

        if ($request->ajax()) {
            return response()->json(['success' => 'Product created successfully!']);
        }

        return redirect()->route('products.index')->with('success', 'Product created successfully!');
    }

    public function edit(Product $product)
    {
        $product->load(['variants', 'images']);
        if (request()->ajax()) {
            return response()->json($product);
        }
        return redirect()->route('products.index');
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'brand_id' => 'required',
            'sku' => 'nullable|unique:products,sku,' . $product->id,
            'price' => 'required|numeric',
        ]);

        DB::transaction(function () use ($request, $product) {
            $productData = $request->only(['name', 'detail', 'category_id', 'brand_id', 'sku', 'price', 'stock', 'status']);
            $productData['slug'] = Str::slug($request->name);

            if ($request->hasFile('image')) {
                if ($product->image) Storage::disk('public')->delete($product->image);
                $productData['image'] = $request->file('image')->store('products', 'public');
            }

            $product->update($productData);

            // Variants
            if ($request->has('variants')) {
                $keepIds = collect($request->variants)->pluck('id')->filter()->toArray();
                $product->variants()->whereNotIn('id', $keepIds)->delete();

                foreach ($request->variants as $variantData) {
                    if(isset($variantData['sku']) && $variantData['sku']) {
                        if (isset($variantData['id'])) {
                            $product->variants()->where('id', $variantData['id'])->update([
                                'sku' => $variantData['sku'],
                                'size' => $variantData['size'] ?? null,
                                'color' => $variantData['color'] ?? null,
                                'price' => $variantData['price'] ?? 0,
                                'stock' => $variantData['stock'] ?? 0,
                            ]);
                        } else {
                            $product->variants()->create($variantData);
                        }
                    }
                }
            } else {
                 // If no variants sent, maybe delete all? Or do nothing?
                 // Usually if empty array sent, it means delete all.
                 // But if field is missing, maybe do nothing.
                 // Assuming form sends variants array if section exists.
            }

            // Multiple Images (Add new ones)
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('products', 'public');
                    $product->images()->create(['image_path' => $path]);
                }
            }
            
            // Delete images if requested
            if ($request->has('delete_images')) {
                foreach ($request->delete_images as $imageId) {
                    $img = ProductImage::find($imageId);
                    if ($img) {
                        Storage::disk('public')->delete($img->image_path);
                        $img->delete();
                    }
                }
            }
        });

        if ($request->ajax()) {
            return response()->json(['success' => 'Product updated successfully!']);
        }

        return redirect()->route('products.index')->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product)
    {
        if ($product->image) Storage::disk('public')->delete($product->image);
        $product->delete();
        return response()->json(['success' => 'Product deleted successfully!']);
    }
}