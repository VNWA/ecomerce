<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BannerController extends Controller
{
    public function loadDataTable()
    {
        $data = Banner::latest()->get();
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
        return Inertia::render('Admin/Banner/Show', ['data' => $data]);
    }
    function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|string',
            'image_mobile' => 'nullable|string',
            'desc' => 'nullable|string',
            'link' => 'nullable|string',
        ]);

        try {
            Banner::create($request->all());
            return response()->json(['message' => 'Uploads Banner Success'], 200);

        } catch (\Throwable $th) {
            \Log::error($th);
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    function showEdit($id)
    {
        $banner = Banner::find($id);

        return Inertia::render('Admin/Banner/Edit', ['banner' => $banner]);
    }
    function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|string',
            'image_mobile' => 'nullable|string',
            'desc' => 'nullable|string',
            'link' => 'required|string',
        ]);

        try {
            $banner = Banner::find($id);
            $banner->update($request->all());
            return response()->json(['message' => 'Update Banner Success'], 200);

        } catch (\Throwable $th) {
            \Log::error($th);
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    function delete(Request $request)
    {

        try {
            Banner::whereIn('id', $request->ids)->delete();
            return response()->json(['message' => 'Delete data success'], 200);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
