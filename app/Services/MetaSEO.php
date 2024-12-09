<?php

namespace App\Services;

use App\Models\Appearance;
use Illuminate\Support\Facades\Cache;

class MetaSEO
{
    protected $title;
    protected $meta_title;
    protected $meta_desc;
    protected $meta_image;
    protected $cacheKey = 'meta_seo';

    public function __construct()
    {
        // Lấy dữ liệu từ cache hoặc load từ database
        $seo = Cache::remember($this->cacheKey, 3600, function () {
            return $this->loadFromDatabase();
        });

        $this->title = $seo['meta_title'] ?? 'Vinawebapp.com';
        $this->meta_title = $seo['meta_title'] ?? 'Vinawebapp.com - Một Công Ty Thiết Kế Website Đi Trước Thời Đại Nhiều Năm Ánh Sáng';
        $this->meta_desc = $seo['meta_desc'] ?? 'Cuối cùng bạn đã đến. Sẵn sàng cho một trang web mới đẹp và hiệu quả? Chúng tôi, nhóm thiết kế web chuyên nghiệp của bạn, đang chờ bạn yêu cầu.';
        $this->meta_image = $seo['meta_image'] ?? url('/images/vnwaLogoIcon.png');
    }

    // Load dữ liệu SEO từ database
    protected function loadFromDatabase()
    {
        $company = Appearance::where('type', 'profile')->first();
        return $company ? ($company->value['seo'] ?? []) : [];
    }

    // Xóa cache và làm mới
    public function refreshCache()
    {
        Cache::forget($this->cacheKey); // Xóa cache cũ
        Cache::remember($this->cacheKey, 3600, function () {
            return $this->loadFromDatabase(); // Tải lại từ DB
        });
    }

    // Set Meta SEO theo ngữ cảnh
    public function setContext($data = [])
    {
        if (isset($data['title'])) {
            $this->title = $data['title'];
        }

        if (isset($data['meta_title'])) {
            $this->meta_title = $data['meta_title'];
        }

        if (isset($data['meta_desc'])) {
            $this->meta_desc = $data['meta_desc'];
        }

        if (isset($data['meta_image'])) {
            $this->meta_image = $data['meta_image'];
        }

        return $this;
    }

    // Lấy Meta SEO (sau khi có thể đã được ghi đè)
    public function getMeta()
    {
        return [
            'title' => $this->title,
            'meta_title' => $this->meta_title,
            'meta_desc' => $this->meta_desc,
            'meta_image' => $this->meta_image,
        ];
    }
}

