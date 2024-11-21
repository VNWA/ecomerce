<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\StaticPage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StaticPageController extends Controller
{
    public function changeIsHeader($id)
    {
        try {
            $staticPage = StaticPage::find($id);
            if ($staticPage->is_header == 1) {
                if ($staticPage->is_header_main == 1) {
                    $staticPage->update(['is_header_main' => 0]);
                }
                $staticPage->update(['is_header' => 0]);

            } else {
                $staticPage->update(['is_header' => 1]);

            }
            return response()->json(['message' => 'Update Header  Success'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function changeIsHeaderMain($id)
    {
        try {
            $staticPage = StaticPage::find($id);
            $value = 0;
            if ($staticPage->is_header_main == 1) {
                $staticPage->update(['is_header_main' => 0]);
            } else {
                // Nếu trang không phải header, đặt nó thành header (1)
                StaticPage::setHeader($id);
            }
            return response()->json(['message' => 'Update Header Main Success'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function loadDataTable()
    {
        $data = StaticPage::latest()->get();
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
        return Inertia::render('Admin/StaticPage/Show', ['data' => $data]);
    }
    function create(Request $request)
    {
        $request->validate([
            'is_header' => 'required|integer|in:1,0',
            'is_header_main' => 'required|integer|in:1,0',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:static_pages,slug',
            'image' => 'required|string',
            'desc' => 'nullable|string',
            'content' => 'required|string',
            'seo_meta.meta_image' => 'nullable|string',
            'seo_meta.meta_title' => 'nullable|string|max:255',
            'seo_meta.meta_desc' => 'nullable|string',
        ]);
        $data = [
            'is_header' => $request->is_header,
            'is_header_main' => $request->is_header_main,
            'name' => $request->name,
            'slug' => $request->slug,
            'image' => $request->image,
            'desc' => $request->desc,
            'content' => $request->content,
            'meta_image' => $request->meta_image,
            'meta_title' => $request->meta_title,
            'meta_desc' => $request->meta_desc
        ];

        try {
            StaticPage::create($data);
            return response()->json(['message' => 'Uploads Static Page Success'], 200);

        } catch (\Throwable $th) {
            \Log::error($th);
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    function showEdit($id)
    {
        $staticPage = StaticPage::find($id);

        return Inertia::render('Admin/StaticPage/Edit', ['staticPage' => $staticPage]);
    }
    function update(Request $request, $id)
    {

        $request->validate([
            'is_header' => 'required|integer|in:1,0',
            'is_header_main' => 'required|integer|in:1,0',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:static_pages,slug,' . $id,
            'image' => 'required|string',
            'desc' => 'nullable|string',
            'content' => 'required|string',
            'seo_meta.meta_image' => 'nullable|string',
            'seo_meta.meta_title' => 'nullable|string|max:255',
            'seo_meta.meta_desc' => 'nullable|string',

        ]);

        $data = [
            'is_header' => $request->is_header,
            'is_header_main' => $request->is_header_main,
            'name' => $request->name,
            'slug' => $request->slug,
            'image' => $request->image,
            'desc' => $request->desc,
            'content' => $request->content,
            'meta_image' => $request->meta_image,
            'meta_title' => $request->meta_title,
            'meta_desc' => $request->meta_desc
        ];
        try {
            if($request->is_header_main == 1){
                StaticPage::where('id', '!=', $id)->update(['is_header_main' => 0]);
            }
            $StaticPage = StaticPage::find($id);
            $StaticPage->update($data);
            return response()->json(['message' => 'Update StaticPage Success'], 200);

        } catch (\Throwable $th) {
            \Log::error($th);
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    function delete(Request $request)
    {

        try {
            StaticPage::whereIn('id', $request->dataId)->delete();
            return response()->json(['message' => 'Delete data success'], 200);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
