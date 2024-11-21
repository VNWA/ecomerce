<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Color;
use App\Models\DeliveryCountry;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductCategoryAssignment;
use App\Models\StaticPage;
use App\Models\Coupon;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use App\Models\Appearance;
use App\Services\MetaSEO;

class ClientController extends Controller
{
    protected $metaSEO;

    public function __construct()
    {
        $this->metaSEO = new MetaSEO();
    }
    public function findCoupon($code)
    {
        try {
            // Tìm coupon theo code
            $coupon = Coupon::where('code', $code)
                ->where('qnt', '>', 0) // Số lượng phải lớn hơn 0
                ->where(function ($query) {
                    $query->where('is_duration', 0)
                        ->orWhere(function ($query) {
                            $query->where('is_duration', 1)
                                ->where('start_time', '<=', Carbon::now())
                                ->where('end_time', '>=', Carbon::now());
                        });
                })
                ->first();

            // Kiểm tra nếu không tìm thấy coupon hoặc coupon không còn hợp lệ
            if (!$coupon) {
                return response()->json(['message' => 'Coupon is invalid or expired.'], 404);
            }

            // Trả về thông tin coupon nếu hợp lệ
            return response()->json([
                'message' => 'Coupon is valid.',
                'coupon' => $coupon
            ], 200);

        } catch (\Exception $e) {
            // Trả về lỗi nếu có ngoại lệ
            return response()->json([
                'message' => 'Failed to retrieve coupon.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function loadProductsColorPages(Request $request)
    {
        $productQuery = Product::query();
        $productQuery->where('is_show', 1);
        $productQuery->whereNotNull('color_id');
        // Kiểm tra xem có tham số 'colors' trong yêu cầu không
        if ($request->has('colors')) {
            $colorSlugs = explode(',', $request->input('colors'));
            $colors = Color::whereIn('slug', $colorSlugs)->get();

            if ($colors->isNotEmpty()) {
                $productQuery->whereIn('color_id', $colors->pluck('id'));
            }
        }

        // Xử lý tham số 'order' để xác định cách sắp xếp
        if ($request->has('order')) {
            $orderParam = $request->input('order');
            $orderParts = explode('-', $orderParam); // tách tham số order
            $column = $orderParts[0]; // cột để sắp xếp
            $direction = isset($orderParts[1]) ? $orderParts[1] : 'asc'; // hướng sắp xếp (mặc định là 'asc')

            // Thêm điều kiện sắp xếp vào truy vấn
            if ($column === 'price') {
                // Sắp xếp theo price_new
                $productQuery->orderBy(DB::raw("(CASE
                    WHEN is_discount = 0 THEN price
                    WHEN discount_type = 'amount' THEN price - discount_price
                    WHEN discount_type = 'percentage' THEN price - (price * (discount_price / 100))
                    ELSE price
                END)"), $direction);
            } else if ($column === 'update_at') {
                // Sắp xếp theo cột update_at
                $productQuery->orderBy('updated_at', $direction);
            } else {
                // Sắp xếp theo cột khác
                $productQuery->orderBy($column, $direction);
            }
        }

        // Sử dụng tham số 'total' để xác định số sản phẩm trên mỗi trang
        $total = $request->input('total', 20); // mặc định là 20 nếu không có tham số total
        $products = $productQuery->latest()->paginate($total)->setPath('');

        return response()->json($products, 200);
    }


    public function loadProducts(Request $request)
    {
        $productQuery = Product::query();
        $productQuery->where('is_show', 1);
        // Kiểm tra xem có tham số 'colors' trong yêu cầu không
        if ($request->has('colors')) {
            $colorSlugs = explode(',', $request->input('colors'));
            $colors = Color::whereIn('slug', $colorSlugs)->get();

            if ($colors->isNotEmpty()) {
                $productQuery->whereIn('color_id', $colors->pluck('id'));
            }
        }

        // Xử lý tham số 'order' để xác định cách sắp xếp
        if ($request->has('order')) {
            $orderParam = $request->input('order');
            $orderParts = explode('-', $orderParam); // tách tham số order
            $column = $orderParts[0]; // cột để sắp xếp
            $direction = isset($orderParts[1]) ? $orderParts[1] : 'asc'; // hướng sắp xếp (mặc định là 'asc')

            // Thêm điều kiện sắp xếp vào truy vấn
            if ($column === 'price') {
                // Sắp xếp theo price_new
                $productQuery->orderBy(DB::raw("(CASE
                    WHEN is_discount = 0 THEN price
                    WHEN discount_type = 'amount' THEN price - discount_price
                    WHEN discount_type = 'percentage' THEN price - (price * (discount_price / 100))
                    ELSE price
                END)"), $direction);
            } else if ($column === 'update_at') {
                // Sắp xếp theo cột update_at
                $productQuery->orderBy('updated_at', $direction);
            } else {
                // Sắp xếp theo cột khác
                $productQuery->orderBy($column, $direction);
            }
        }

        // Sử dụng tham số 'total' để xác định số sản phẩm trên mỗi trang
        $total = $request->input('total', 20); // mặc định là 20 nếu không có tham số total
        $products = $productQuery->latest()->paginate($total)->setPath('');

        return response()->json($products, 200);
    }


    public function loadDataCategoryPage($slug)
    {

        $category = ProductCategory::where('slug', $slug)->first([
            'banner_image',
            'image',
            'name',
            'desc',
            'meta_image',
            'meta_title',
            'meta_desc',
        ]);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        $subCategories = ProductCategory::where('parent_id', $category->id)->get([
            'parent_id',
            'name',
            'slug',
        ]);
        $this->metaSEO->setTitle($category->name)
            ->setMetaTitle($category->meta_title ?? null)
            ->setMetaDesc($category->meta_desc ?? null)
            ->setMetaImage($category->meta_image ?? null);

        $seo = $this->metaSEO->getMeta();

        return response()->json([
            'category' => $category,
            'subCategories' => $subCategories,
            'seo' => $seo,
        ], 200);

    }


    public function loadProductsByCategory(Request $request, $slug)
    {
        // Tìm danh mục theo slug
        $category = ProductCategory::where('slug', $slug)->first(['id']);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        // Lấy danh sách sản phẩm thuộc danh mục
        $listIdProduct = ProductCategoryAssignment::where('product_category_id', $category->id)
            ->pluck('product_id')
            ->toArray();

        $productQuery = Product::whereIn('id', $listIdProduct);
        $productQuery->where('is_show', 1);

        // Lọc theo màu (nếu có)
        if ($request->has('colors')) {
            $colorSlugs = explode(',', $request->input('colors'));
            $colors = Color::whereIn('slug', $colorSlugs)->pluck('id');

            if ($colors->isNotEmpty()) {
                $productQuery->whereIn('color_id', $colors);
            }
        }

        // Xử lý tham số 'order' để sắp xếp
        if ($request->filled('order')) {
            [$column, $direction] = explode('-', $request->input('order')) + [1 => 'asc'];

            if ($column === 'price') {
                // Sắp xếp theo giá, ưu tiên giá đã giảm nếu có
                $productQuery->orderBy(DB::raw("
                    (CASE
                        WHEN is_discount = 0 THEN price
                        WHEN discount_type = 'amount' THEN price - discount_price
                        WHEN discount_type = 'percentage' THEN price - (price * (discount_price / 100))
                        ELSE price
                    END)
                "), $direction);
            } elseif ($column === 'updated_at') {
                // Sắp xếp theo ngày cập nhật
                $productQuery->orderBy('updated_at', $direction);
            } else {
                // Sắp xếp theo cột khác
                $productQuery->orderBy($column, $direction);
            }
        }

        // Số sản phẩm trên mỗi trang (mặc định 20)
        $total = $request->input('total', 20);

        // Lấy danh sách sản phẩm và phân trang
        $products = $productQuery->paginate($total)->setPath('');

        return response()->json($products, 200);
    }

    public function loadDataDetailProduct($slug)
    {
        $product = Product::where('slug', $slug)
            ->with('categories:name,slug')
            ->with('brand:id,name,slug')
            ->with('color:id,name,slug')
            ->where('is_show', 1)
            ->first();
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        return response()->json($product, 200);

    }
    public function checkProductSku($sku)
    {
        $product = Product::where('sku', $sku)->where('is_show', 1)->first(['name', 'sku', 'slug', 'images', 'price', 'discount_type', 'discount_price', 'is_discount']);
        if ($product) {
            $images = $product->images;
            $product->image = $images[0];
        }
        return response()->json($product, 200);
    }
    protected function getChilProductdCategories($parentId)
    {
        // Lấy danh mục con của $parentId đã sắp xếp theo 'ord'
        $children = ProductCategory::where('parent_id', $parentId)
            ->orderBy('ord')
            ->get(['id', 'parent_id', 'name', 'slug', 'ord'])
            ->map(function ($category) {
                // Đệ quy lấy danh mục con của danh mục hiện tại
                $category->children = $this->getChilProductdCategories($category->id);
                return $category;
            });

        return $children;
    }

    public function loadDataLayout()
    {
        $logo = Appearance::where('type', 'logo')->first();
        $footer = Appearance::where('type', 'footer')->first();
        $topNav = Appearance::where('type', 'top_nav')->first();
        $profile = Appearance::where('type', 'profile')->first();
        $product_categories = ProductCategory::where('is_show', 1)->where('is_header', 1)
            ->orderBy('ord')
            ->get(['id', 'parent_id', 'is_header', 'slug', 'is_header_sub_colors', 'header_bg', 'image', 'name', 'ord'])
            ->map(function ($category) {
                // Đệ quy lấy danh mục con
                $category->children = $this->getChilProductdCategories($category->id);
                return $category;
            });
        $colors = Color::get(['name', 'image', 'slug']);
        $staticPage = [];

        $staticPage['main'] = StaticPage::where('is_show', 1)->where('is_header', 1)->first(['name', 'slug']);
        if ($staticPage['main']) {
            $staticPage['menu'] = StaticPage::where('is_show', 1)->whereNot('is_header', 1)->get(['name', 'slug']);
        }

        return response()->json([
            'logo' => $logo ? $logo->value : [],
            'footer' => $footer ? $footer->value : [],
            'topNav' => $topNav ? $topNav->value : [],
            'profile' => $profile ? $profile->value : [],
            'product_categories' => $product_categories,
            'colors' => $colors,
            'staticPage' => $staticPage,
        ], 200);
    }
    public function loadDataHomePage()
    {
        $profile = Appearance::where('type', 'profile')->first();

        $banners = Banner::where('is_show', 1)
            ->orderBy('ord', 'asc')
            ->get(['name', 'link', 'image', 'image_mobile']);

        // Kiểm tra và gán image_mobile nếu rỗng
        $banners = $banners->map(function ($banner) {
            if (empty($banner->image_mobile)) {
                $banner->image_mobile = $banner->image;
            }
            return $banner;
        });
        $highlightCategories = ProductCategory::where('is_show', 1)->where('is_highlight', 1)
            ->orderBy('ord', 'asc')
            ->get(['name', 'slug', 'image']);

        $homeSections = Appearance::where('type', 'homeSections')->first();
        $sellerProducts = Product::where('is_show', 1)->where('is_seller', 1)->get(['name', 'images', 'slug', 'price']);
        foreach ($sellerProducts as $value) {
            $images = $value->images;
            $value->image = $images[0];
        }

        return response()->json([
            'banners' => $banners,
            'highlightCategories' => $highlightCategories,
            'homeSections' => $homeSections->value,
            'sellerProducts' => $sellerProducts,
            'profile' => $profile->value,

        ], 200);
    }
    public function loadDataDeliveryCountries()
    {
        $deliveryCountries = DeliveryCountry::with(['deliveryPrices.delivery'])->get();

        // Format dữ liệu trả về
        $result = $deliveryCountries->map(function ($country) {
            return [
                'value' => $country->id,
                'title' => $country->name,
                'deliveries' => $country->deliveryPrices->map(function ($deliveryPrice) {
                    return [
                        'id' => $deliveryPrice->delivery->id,
                        'name' => $deliveryPrice->delivery->name,
                        'image' => $deliveryPrice->delivery->image,
                        'price' => $deliveryPrice->price,
                    ];
                }),
            ];
        });
        return response()->json($result, 200);
    }
    public function loadDataStaticPage($slug)
    {
        try {
            $staticPage = StaticPage::where('is_show', 1)->where('slug', $slug)->first();
            if ($staticPage) {

                return response()->json(['message' => 'Success', 'data' => $staticPage], 200);
            } else {

                return response()->json(['message' => 'Data Not Found'], 404);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);

        }
    }
}
