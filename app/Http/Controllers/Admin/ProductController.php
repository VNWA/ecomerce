<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Brand;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductCategoryAssignment;
use DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Validator;

class ProductController extends Controller
{
    public function changeIsSeller($id)
    {
        try {
            $product = Product::find($id);
            if ($product->is_seller == 1) {
                $product->update(['is_seller' => 0]);
                return response()->json(['message' => 'Un Active Product Seller Success'], 200);
            } else {
                $product->update(['is_seller' => 1]);
                return response()->json(['message' => 'Set Active Product Seller Success'], 200);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function loadMiniProducts()
    {
        $products = Product::where('is_show', 1)->get(['id', 'name', 'price', 'slug', 'images']);

        $transformedProducts = $products->map(function ($product) {
            $image = $product->images;
            return [
                'value' => $product->name . "_" . $product->slug . "_" . $product->price . "_" . $product->price_new . "_" . $image[0],
                'label' => $product->name,
            ];
        });
        return response()->json($transformedProducts, 200);
    }
    public function loadDataCategoriesAndBrands()
    {
        try {
            $categories = ProductCategory::get(['id', 'name']);
            $brands = Brand::get(['id', 'name']);
            return response()->json(['categories' => $categories, 'brands' => $brands], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    protected function getChildCategories($parentId)
    {
        // Lấy danh mục con của $parentId đã sắp xếp theo 'ord'
        $children = ProductCategory::where('parent_id', $parentId)
            ->orderBy('ord')
            ->get(['id', 'parent_id', 'name', 'ord'])
            ->map(function ($category) {
                // Đệ quy lấy danh mục con của danh mục hiện tại
                $category->children = $this->getChildCategories($category->id);
                return $category;
            });

        return $children;
    }
    public function loadDataCategoriesTreeAndBrandsAndColors()
    {
        try {
            $categories = ProductCategory::whereNull('parent_id')
                ->orderBy('ord')
                ->get(['id', 'parent_id', 'name', 'ord'])
                ->map(function ($category) {
                    // Đệ quy lấy danh mục con
                    $category->children = $this->getChildCategories($category->id);
                    return $category;
                });
            $brands = Brand::get(['id', 'name']);
            $colors = Color::get(['id', 'name']);
            return response()->json(['categories' => $categories, 'brands' => $brands, 'colors' => $colors], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function loadDataTable(Request $request)
    {
        // Lấy các tham số từ request
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        $sortBy = $request->get('sortBy', 'created_at');
        $sortType = $request->get('sortType', 'desc');
        $name = $request->get('name');
        $categoryIds = $request->get('categories_id', []);
        $brandIds = $request->get('brands_id', []);

        // Khởi tạo query
        $query = Product::query();

        // Lọc theo tên product nếu có
        if ($name) {
            $query->where('name', 'like', "%$name%");
        }

        // Lọc theo danh mục nếu có
        if (!empty($categoryIds)) {
            $query->whereHas('categories', function ($q) use ($categoryIds) {
                $q->whereIn('product_categories.id', $categoryIds); // Sửa chỗ này
            });
        }
        if (!empty($brandIds)) {
            $query->whereHas('brand', function ($q) use ($brandIds) { // Dùng 'brand' (số ít)
                $q->whereIn('brands.id', $brandIds); // Lọc theo brand IDs
            });
        }


        // Sắp xếp và phân trang
        $total = $query->count();
        $products = $query->skip(($page - 1) * $perPage)->take($perPage)->with('categories')->with('brand')->orderBy($sortBy, $sortType)->get();

        return response()->json([
            'data' => $products,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => ceil($total / $perPage),
        ], 200);
    }
    public function showEdit($id)
    {
        $product = Product::with('categories')->find($id);
        $categoriesId = $product->categories->pluck('id');
        $product->categoriesId = $categoriesId;

        return Inertia::render('Admin/Ecommerce/Product/Edit', ['product' => $product]);

    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'is_show' => 'required|integer|in:1,0',
            'images' => 'required|array',
            'images.*' => 'string',
            'brand_id' => 'nullable|exists:brands,id',
            'color_id' => 'nullable|exists:colors,id',
            'sku' => 'required|string|unique:products,sku',
            'size' => 'nullable|string|max:255',
            'included' => 'nullable|string|max:255',
            'stock' => 'required|integer|min:0',
            'availability' => 'required|integer|in:1,0',
            'origin' => 'required|string|max:255',
            'ean' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:urls,slug',
            'price' => 'required|integer|min:0',
            'is_discount' => 'required|integer|in:1,0',
            'discount_type' => 'nullable|in:percentage,amount',
            'discount_price' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'ingredients' => 'nullable|string',
            'how_to_use' => 'nullable|string',
            'seo_meta.meta_image' => 'nullable|string',
            'seo_meta.meta_title' => 'nullable|string|max:255',
            'seo_meta.meta_desc' => 'nullable|string|max:255',
            'parentIds' => 'nullable|array',
            'parentIds.*' => 'integer|exists:product_categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Kiểm tra discount
        if ($request->is_discount == 1) {
            $errors = [];
            if (empty($request->discount_type)) {
                $errors['discount_type'] = ['Discount type is required when discount is applied.'];
            }

            if ($request->discount_type === 'amount' && $request->discount_price > $request->price) {
                $errors['discount_price'] = ['The discount price must not be greater than the price.'];
            } elseif ($request->discount_type === 'percentage' && $request->discount_price > 100) {
                $errors['discount_price'] = ['The discount percentage cannot be greater than 100.'];
            }

            if (!empty($errors)) {
                return response()->json(['errors' => $errors], 422);
            }
        }


        $data = [
            'is_show' => $request->is_show,
            'images' => $request->images,
            'brand_id' => $request->brand_id,
            'color_id' => $request->color_id,
            'sku' => $request->sku,
            'size' => $request->size,
            'included' => $request->included,
            'stock' => $request->stock,
            'availability' => $request->availability,
            'origin' => $request->origin,
            'ean' => $request->ean,
            'name' => $request->name,
            'slug' => $request->slug,
            'price' => $request->price,
            'is_discount' => $request->is_discount,
            'discount_type' => $request->discount_type,
            'discount_price' => $request->discount_price,
            'description' => $request->description,
            'ingredients' => $request->ingredients,
            'how_to_use' => $request->how_to_use,
            'meta_image' => $request->seo_meta['meta_image'] ?? null,
            'meta_title' => $request->seo_meta['meta_title'] ?? null,
            'meta_desc' => $request->seo_meta['meta_desc'] ?? null,
        ];

        DB::beginTransaction();

        try {
            // Tạo product
            $product = Product::create($data);

            // Liên kết product với các danh mục
            if ($request->parentIds) {
                foreach ($request->parentIds as $parentId) {
                    ProductCategoryAssignment::create([
                        'product_id' => $product->id,
                        'product_category_id' => $parentId,
                    ]);
                }
            }

            DB::commit();
            return response()->json(['message' => 'Create Product Success'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            \Log::error($th);

            return response()->json(['message' => 'Server Error: ' . $th->getMessage()], 500);
        }
    }

    function update(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Error'], 500);

        }
        $validator = Validator::make($request->all(), [
            'is_show' => 'required|integer|in:1,0',
            'images' => 'required|array',
            'images.*' => 'string',
            'brand_id' => 'nullable|exists:brands,id',
            'color_id' => 'nullable|exists:colors,id',
            'sku' => 'required|string|unique:products,sku,' . $product->id,
            'size' => 'nullable|string|max:255',
            'included' => 'nullable|string|max:255',
            'stock' => 'required|integer|min:0',
            'availability' => 'required|integer|in:1,0',
            'origin' => 'required|string|max:255',
            'ean' => 'nullable|string|max:255|unique:products,ean,' . $product->id,
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:urls,slug,' . $product->url->id,
            'price' => 'required|integer|min:0',
            'is_discount' => 'required|integer|in:1,0',
            'discount_type' => 'nullable|in:percentage,amount',
            'discount_price' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'ingredients' => 'nullable|string',
            'how_to_use' => 'nullable|string',
            'seo_meta.meta_image' => 'nullable|string',
            'seo_meta.meta_title' => 'nullable|string|max:255',
            'seo_meta.meta_desc' => 'nullable|string|max:255',
            'parentIds' => 'nullable|array',
            'parentIds.*' => 'integer|exists:product_categories,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Kiểm tra discount
        if ($request->is_discount == 1) {
            $errors = [];
            if (empty($request->discount_type)) {
                $errors['discount_type'] = ['Discount type is required when discount is applied.'];
            }

            if ($request->discount_type === 'amount' && $request->discount_price > $request->price) {
                $errors['discount_price'] = ['The discount price must not be greater than the price.'];
            } elseif ($request->discount_type === 'percentage' && $request->discount_price > 100) {
                $errors['discount_price'] = ['The discount percentage cannot be greater than 100.'];
            }

            if (!empty($errors)) {
                return response()->json(['errors' => $errors], 422);
            }
        }

        $data = [
            'is_show' => $request->is_show,
            'images' => $request->images,
            'brand_id' => $request->brand_id,
            'color_id' => $request->color_id,
            'sku' => $request->sku,
            'size' => $request->size,
            'included' => $request->included,
            'stock' => $request->stock,
            'availability' => $request->availability,
            'origin' => $request->origin,
            'ean' => $request->ean,
            'name' => $request->name,
            'slug' => $request->slug,
            'price' => $request->price,
            'is_discount' => $request->is_discount,
            'discount_type' => $request->discount_type,
            'discount_price' => $request->discount_price,
            'description' => $request->description,
            'ingredients' => $request->ingredients,
            'how_to_use' => $request->how_to_use,
            'meta_image' => $request->seo_meta['meta_image'] ?? null,
            'meta_title' => $request->seo_meta['meta_title'] ?? null,
            'meta_desc' => $request->seo_meta['meta_desc'] ?? null,
        ];
        DB::beginTransaction();

        try {
            // Tạo product
            $product->update($data);
            ProductCategoryAssignment::where('product_id', $id)->delete();

            // Liên kết product với các danh mục
            foreach ($request->parentIds as $parentId) {
                ProductCategoryAssignment::create([
                    'product_id' => $id,
                    'product_category_id' => $parentId,
                ]);
            }
            DB::commit();
            return response()->json(['message' => 'Update Product Success'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            \Log::error($th);

            return response()->json(['message' => $th->getMessage()], 500);
        }

    }



    function delete(Request $request)
    {

        try {
            Product::whereIn('id', $request->dataId)->delete();
            return response()->json(['message' => 'Delete data success'], 200);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
