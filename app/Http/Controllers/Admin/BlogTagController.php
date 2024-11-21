<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\BlogTag;
use Illuminate\Http\Request;

class BlogTagController extends Controller
{
    public function loadDataTable()
    {
        $tags = BlogTag::latest()->get();
        return response()->json($tags, 200);
    }

    function create(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:urls,slug',
        ]);
        $data = [];
        $data['name'] = $request->name;
        $data['slug'] = $request->slug;
        try {
            $category = BlogTag::create($data);
            return response()->json(['message' => 'Uploads Tag Blog Success'], 200);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 507);

        }
    }
    public function update(Request $request, $id)
    {
        $BlogTag = BlogTag::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:urls,slug,' . $BlogTag->url->id,
        ]);

        $data = $request->only(['name', 'slug']);

        try {
            $BlogTag = BlogTag::findOrFail($id);
            $BlogTag->update($data);

            return response()->json(['message' => 'Update Category Blog Success'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    function delete(Request $request)
    {

        try {
            BlogTag::whereIn('id', $request->dataId)->delete();
            return response()->json(['message' => 'Delete data success'], 200);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
