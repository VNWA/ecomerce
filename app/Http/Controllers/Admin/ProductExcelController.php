<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Imports\ProductsImport;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductCategoryAssignment;
use DB;
use Illuminate\Http\Request;
use App\Exports\ProductExport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use Str;

class ProductExcelController extends Controller
{
    public function export()
    {
        return Excel::download(new ProductExport, 'product_template.xlsx');

    }
    public function read(Request $request)
    {
        // Validate file được tải lên
        $request->validate([
            'file' => 'required|mimes:xlsx',
        ]);

        // Đọc file Excel
        $file = $request->file('file');

        // Sử dụng ProductsImport để đọc dữ liệu
        $excel = Excel::toCollection(new ProductsImport, $file);

        // Khởi tạo mảng chứa dữ liệu kết quả
        $data = [];

        // Duyệt qua từng dòng trong file Excel
        foreach ($excel[0] as $key => $value) {
            // Khởi tạo phần tử mới cho mảng $data với dữ liệu từ file Excel
            $data[$key] = $value;

            // Tìm Brand theo brand_id
            $brand = Brand::find($value['brand_id']);
            if ($brand) {
                $data[$key]['brand_name'] = $brand->name;
            } else {
                // Nếu không tìm thấy brand_id hợp lệ, gán null
                $data[$key]['brand_id'] = null;
                $data[$key]['brand_name'] = 'Unknown'; // Hoặc hiển thị "Unknown"
            }

            // Xử lý hình ảnh (nếu có) và loại bỏ khoảng trắng thừa
            if (isset($value['images'])) {
                // Tách chuỗi images thành mảng và loại bỏ khoảng trắng thừa
                $images = preg_split('/\s*,\s*/', trim($value['images']));
                // Gán lại images dưới dạng mảng
                $data[$key]['images'] = array_map('trim', $images); // Sử dụng array_map để loại bỏ khoảng trắng
            }

            // Kiểm tra nếu tồn tại categories_id
            if (isset($value['categories_id'])) {
                // Tách chuỗi categories_id thành mảng các số, loại bỏ khoảng trắng thừa
                $categoryIds = preg_split('/\s+/', trim($value['categories_id']));

                // Tìm các Category hợp lệ trong bảng ProductCategory
                $validCategories = ProductCategory::whereIn('id', $categoryIds)->get(['id', 'name']);

                // Lấy các Category ID hợp lệ
                $validCategoryIds = $validCategories->pluck('id')->toArray();

                // Lấy tên các Category hợp lệ
                $validCategoryNames = $validCategories->pluck('name')->toArray();

                // Gán lại các category hợp lệ vào chuỗi ID
                $data[$key]['categories_id'] = implode(' ', $validCategoryIds);

                // Gán tên các category hợp lệ vào mảng categories_name
                $data[$key]['categories_name'] = $validCategoryNames;

                // Xóa các category không hợp lệ
                $invalidCategories = array_diff($categoryIds, $validCategoryIds);

                // Nếu có category không hợp lệ, bạn có thể xử lý
                if (!empty($invalidCategories)) {
                    $data[$key]['invalid_categories'] = implode(' ', $invalidCategories);
                }
            }
            if (isset($value['catalogs'])) {
                // Tách chuỗi catalogs thành mảng và loại bỏ khoảng trắng thừa
                $catalogs = preg_split('/\s*,\s*/', trim($value['catalogs']));
                // Gán lại catalogs dưới dạng mảng
                $data[$key]['catalogs'] = array_map('trim', $catalogs); // Sử dụng array_map để loại bỏ khoảng trắng
            }

        }

        // Trả về dữ liệu đã đọc từ file Excel
        return response()->json($data);
    }
    public function import(Request $request)
    {
        // Validate file upload
        $request->validate([
            'file' => 'required|mimes:xlsx',
        ]);

        // Đọc file Excel
        $file = $request->file('file');

        // Sử dụng ProductsImport để đọc dữ liệu
        $excel = Excel::toCollection(new ProductsImport, $file);

        // Duyệt qua từng dòng trong file Excel
        foreach ($excel[0] as $key => $value) {
            DB::beginTransaction(); // Mở transaction cho từng dòng
            try {
                $images = [];
                $catalogs = [];

                // Xử lý images
                if (!empty($value['images'])) {
                    $images = preg_split('/\s*,\s*/', trim($value['images']));
                    $images = array_map('trim', $images); // Loại bỏ khoảng trắng thừa
                }

                // Xử lý catalogs
                if (!empty($value['catalogs'])) {
                    $catalogs = preg_split('/\s*,\s*/', trim($value['catalogs']));
                    $catalogs = array_map('trim', $catalogs); // Loại bỏ khoảng trắng thừa
                }

                // Tạo slug và kiểm tra unique
                $slugBase = Str::slug($value['name']);
                $slug = $slugBase;
                $slugCount = Product::where('slug', 'LIKE', "$slugBase%")->count();

                if ($slugCount > 0) {
                    $slug = $slugBase . '-' . ($slugCount + 1); // Thêm hậu tố nếu slug trùng lặp
                }

                // Chuẩn bị dữ liệu để lưu
                $data = [
                    'is_show' => 1,
                    'name' => $value['name'] ?? 'Unknown Product', // Kiểm tra nếu không có name
                    'slug' => $slug,
                    'images' => $images,
                    'description' => $value['description'] ?? '',
                    'tech_info' => $value['tech_info'] ?? '',
                    'brand_id' => $value['brand_id'] ?? null, // Đảm bảo có giá trị mặc định
                    'sku' => $value['sku'] ?? '',
                    'stock' => $value['stock'] ?? 0,
                    'origin' => $value['origin'] ?? '',
                    'catalogs' => $catalogs,
                ];

                // Tạo product
                $product = Product::create($data);

                // Liên kết product với các danh mục (nếu có)
                if (!empty($value['categories_id'])) {
                    $categoryIds = preg_split('/\s+/', trim($value['categories_id']));
                    $validCategories = ProductCategory::whereIn('id', $categoryIds)->get(['id']);

                    // Lấy các Category ID hợp lệ và tạo liên kết
                    $validCategoryIds = $validCategories->pluck('id')->toArray();
                    foreach ($validCategoryIds as $parentId) {
                        ProductCategoryAssignment::create([
                            'product_id' => $product->id,
                            'product_category_id' => $parentId,
                        ]);
                    }
                }

                DB::commit(); // Commit dữ liệu cho sản phẩm hiện tại

            } catch (\Throwable $th) {
                DB::rollBack(); // Rollback nếu có lỗi trong quá trình xử lý từng dòng
                // Log lỗi để kiểm tra sau
                Log::error('Error importing product at row ' . ($key + 1) . ': ' . $th->getMessage());
                // Tiếp tục với dòng tiếp theo mà không return hay dừng lại
            }
        }

        return response()->json(['message' => 'Import process completed. Check logs for any errors.'], 200);
    }

}
