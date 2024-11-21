<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\ProductCategory;
use DB;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductCategoryController extends Controller
{
    public function loadDataTable()
    {
        // Lấy danh mục gốc (parent_id null) đã sắp xếp theo 'ord'
        $categories = ProductCategory::whereNull('parent_id')
            ->orderBy('ord')
            ->get(['id', 'parent_id', 'is_header', 'name', 'ord'])
            ->map(function ($category) {
                // Đệ quy lấy danh mục con
                $category->children = $this->getChildCategories($category->id);
                return $category;
            });
        return $categories;
    }

    // Đệ quy lấy danh mục con
    protected function getChildCategories($parentId)
    {
        // Lấy danh mục con của $parentId đã sắp xếp theo 'ord'
        $children = ProductCategory::where('parent_id', $parentId)
            ->orderBy('ord')
            ->get(['id', 'parent_id', 'is_header', 'name', 'ord'])
            ->map(function ($category) {
                // Đệ quy lấy danh mục con của danh mục hiện tại
                $category->children = $this->getChildCategories($category->id);
                return $category;
            });
        return $children;
    }

    public function index()
    {
        $data = $this->loadDataTable();
        return Inertia::render('Admin/Ecommerce/ProductCategories', ['data' => $data]);
    }
    function loadNewDataTree()
    {
        $data = $this->loadDataTable();
        return response()->json($data, 200);
    }
    protected function updateTreeRecursive($parentId, $nodes)
    {
        foreach ($nodes as $index => $node) {
            $categoryId = $node['id'];
            $newOrd = $index + 1;
            ProductCategory::where('id', $categoryId)->update([
                'parent_id' => $parentId,
                'ord' => $newOrd,
            ]);

            if (isset($node['children']) && count($node['children']) > 0) {
                $this->updateTreeRecursive($categoryId, $node['children']);
            }
        }
    }
    public function updateTree(Request $request)
    {
        $treeData = $request->input('treeData');

        // Sử dụng transaction để đảm bảo tính nhất quán
        DB::beginTransaction();

        try {
            // Gọi hàm đệ quy để cập nhật cây dữ liệu
            $this->updateTreeRecursive(null, $treeData);
            DB::commit();
            return response()->json(['message' => 'The article category data tree has been updated successfully'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Unable to update article category tree', 'error' => $e->getMessage()], 500);
        }
    }
    function getDetailCategory($id)
    {
        try {
            $data = ProductCategory::findOrFail($id);
            return response()->json($data, 200);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:urls,slug',
        ]);

        $data = [];
        $data['parent_id'] = $request->parentId;
        $data['name'] = $request->name;
        $data['slug'] = $request->slug;
        $data['desc'] = $request->desc;
        $data['icon'] = $request->icon;
        $data['image'] = $request->image;
        $data['banner_image'] = $request->banner_image;
        $data['is_highlight'] = $request->is_highlight;
        $data['is_show'] = $request->is_show;
        $data['is_header'] = $request->is_header;
        $data['is_header_sub_colors'] = $request->is_header_sub_colors;
        $data['header_bg'] = $request->header_bg;
        $data['meta_title'] = $request->seo->meta_title ?? null;
        $data['meta_desc'] = $request->seo->meta_desc ?? null;
        $data['meta_image'] = $request->seo->meta_image ?? null;
        try {
            $category = ProductCategory::create($data);
            return response()->json(['message' => 'Uploads Category Blog Success', 'id' => $category->id], 200);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 507);

        }

    }
    public function update(Request $request, $id)
    {
        $ProductCategory = ProductCategory::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:urls,slug,' . $ProductCategory->url->id,
        ]);

        $data = [
            'parent_id' => $request->parentId,
            'slug' => $request->slug,
            'name' => $request->name,
            'desc' => $request->desc,
            'icon' => $request->icon,
            'image' => $request->image,
            'banner_image' => $request->banner_image,
            'is_highlight' => $request->is_highlight,
            'is_show' => $request->is_show,
            'is_header' => $request->is_header,
            'is_header_sub_colors' => $request->is_header_sub_colors,
            'header_bg' => $request->header_bg,
            'meta_title' => $request->seo['meta_title'] ?? null,
            'meta_desc' => $request->seo['meta_desc'] ?? null,
            'meta_image' => $request->seo['meta_image'] ?? null,
        ];

        try {

            if ($request->has('parent_id') && $ProductCategory->parent_id != $request->input('parent_id')) {
                // Thiết lập lại giá trị `ord` khi thay đổi `parent_id`
                $maxOrd = ProductCategory::where('parent_id', $request->input('parent_id'))->max('ord');
                $data['ord'] = $maxOrd + 1;
            }

            $ProductCategory->update($data);

            return response()->json(['message' => 'Update Category Blog Success', 'id' => $id], 200);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    function delete($id)
    {
        try {
            // Tìm category blog cần xóa
            $ProductCategory = ProductCategory::findOrFail($id);

            // Xóa category blog
            DB::transaction(function () use ($ProductCategory) {
                // Xóa bất kỳ liên kết nào (nếu có) trước khi xóa category blog
                // Ví dụ: nếu có các liên kết với các bài viết, bạn có thể xóa chúng ở đây
                // $ProductCategory->posts()->delete();

                // Tiến hành xóa category blog
                $ProductCategory->delete();
            });

            return response()->json([
                'message' => 'Successfully deleted blog category.'
            ]);
        } catch (\Exception $e) {
            // Xử lý lỗi nếu có
            return response()->json([
                'message' => 'An error occurred while deleting blog category.'
            ], 500);


        }
    }

}
