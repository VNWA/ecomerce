<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Brand;
use App\Models\ListSlug;
use App\Models\ListImage;
use App\Models\ListView;

class BrandController extends Controller
{
    public function loadDataTable()
    {
        $data = Brand::latest()->get();
        foreach ($data as $key => $value) {
            $value->create_time = Carbon::parse($value->created_at)->format('H:i , d/m/Y ');
            $value->update_time = Carbon::parse($value->updated_at)->format('H:i , d/m/Y ');

        }

        return response()->json(['data' => $data]);
    }
    function showIndex()
    {
        $jsonData = $this->loadDataTable()->getContent(); // Lấy nội dung JSON response
        $data = json_decode($jsonData, true)['data']; // Giải mã JSON và lấy giá trị của 'data'
        return Inertia::render('Admin/Ecommerce/Brand/Show', ['data' => $data]);
    }
    function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|string',
            'desc' => 'nullable|string',
            'slug' => 'nullable|string',
        ]);

        try {
            Brand::create($request->all());
            return response()->json(['message' => 'Uploads Brand Success'], 200);

        } catch (\Throwable $th) {
            \Log::error($th);
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    function showEdit($id)
    {
        $brand = Brand::find($id);

        return Inertia::render('Admin/Ecommerce/Brand/Edit', ['brand' => $brand]);
    }
    function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|string',
            'desc' => 'nullable|string',
            'slug' => 'nullable|string',
        ]);

        try {
            $brand = Brand::find($id);
            $brand->update($request->all());
            return response()->json(['message' => 'Update Brand Success'], 200);

        } catch (\Throwable $th) {
            \Log::error($th);
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    function delete(Request $request)
    {

        try {
            Brand::whereIn('id', $request->dataId)->delete();
            return response()->json(['message' => 'Delete data success'], 200);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

}
