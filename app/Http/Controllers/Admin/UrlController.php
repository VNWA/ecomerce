<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Url;
use Illuminate\Http\Request;

class UrlController extends Controller
{
    public function checkSlug($slug, $id = null, $model_type = null)
    {
        // Tìm kiếm slug trong bảng urls
        $query = Url::where('slug', $slug);

        // Nếu có ID, loại bỏ slug của model hiện tại
        if ($id && $model_type) {
            $query->where('model_id', '!=', $id)->where('model_type', '!=', $model_type);
        }

        // Kiểm tra xem slug có tồn tại hay không
        $exists = $query->exists();

        // Trả về kết quả
        if ($exists) {
            return response()->json(['type' => 'error', 'message' => 'Slug already exists.'], 409);
        } else {
            return response()->json(['type' => 'success', 'message' => 'Slug is available.'], 200);
        }
    }
}
