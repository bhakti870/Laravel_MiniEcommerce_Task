<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:brand-list|brand-create|brand-edit|brand-delete', ['only' => ['index','show']]);
         $this->middleware('permission:brand-create', ['only' => ['create','store']]);
         $this->middleware('permission:brand-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:brand-delete', ['only' => ['destroy']]);
    }
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $brands = Brand::latest()->get();
            return DataTables::of($brands)
                ->addIndexColumn()
                ->addColumn('logo', function ($row) {
                    return $row->logo 
                        ? '<img src="'.asset('storage/'.$row->logo).'" width="50" height="50">' 
                        : '-';
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
                ->rawColumns(['logo', 'status', 'action'])
                ->make(true);
        }
        return view('brands.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:brands,name,' . $request->id,
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        $data = [
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'status' => $request->status
        ];

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('brands', 'public');
        }

        Brand::updateOrCreate(['id' => $request->id], $data);

        return response()->json(['success' => 'Brand saved successfully!']);
    }

    public function edit($id)
    {
        return Brand::find($id);
    }

    public function destroy($id)
    {
        Brand::find($id)->delete();
        return response()->json(['success' => 'Brand deleted successfully!']);
    }
}
