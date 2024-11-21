<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Color;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ColorController extends Controller
{
    public function loadDataTable()
    {
        $data = Color::latest()->get();
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
        return Inertia::render('Admin/Ecommerce/Color/Show', ['data' => $data]);
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
            Color::create($request->all());
            return response()->json(['message' => 'Uploads Color Success'], 200);

        } catch (\Throwable $th) {
            \Log::error($th);
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    function showEdit($id)
    {
        $brand = Color::find($id);

        return Inertia::render('Admin/Ecommerce/Color/Edit', ['brand' => $brand]);
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
            $brand = Color::find($id);
            $brand->update($request->all());
            return response()->json(['message' => 'Update Color Success'], 200);

        } catch (\Throwable $th) {
            \Log::error($th);
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    function delete(Request $request)
    {

        try {
            Color::whereIn('id', $request->dataId)->delete();
            return response()->json(['message' => 'Delete data success'], 200);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
