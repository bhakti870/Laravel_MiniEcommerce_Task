<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:category-list|category-create|category-edit|category-delete', ['only' => ['index','show']]);
        $this->middleware('permission:category-create', ['only' => ['create','store']]);
        $this->middleware('permission:category-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:category-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $categories = Category::with('parent')->latest()->get();

            return DataTables::of($categories)
                ->addIndexColumn()
                ->addColumn('parent_name', function ($row) {
                    return $row->parent ? $row->parent->name : '-';
                })
                ->addColumn('status', function ($row) {
                    return $row->status
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-danger">Inactive</span>';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <button class="btn btn-primary btn-sm editBtn" data-id="'.$row->id.'">Edit</button>
                        <button class="btn btn-danger btn-sm deleteBtn" data-id="'.$row->id.'">Delete</button>
                    ';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        $parentCategories = Category::where('status', 1)->get();
        return view('categories.index', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:categories,name,' . $request->id,
            'parent_id' => 'nullable|exists:categories,id'
        ]);

        Category::updateOrCreate(
            ['id' => $request->id],
            [
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'parent_id' => $request->parent_id,
                'status' => $request->status ?? 0
            ]
        );

        return response()->json(['success' => 'Category saved successfully!']);
    }

    public function edit($id)
    {
        return Category::findOrFail($id);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json(['success' => 'Category deleted successfully!']);
    }
}
