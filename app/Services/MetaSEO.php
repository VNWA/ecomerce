<?php
// app/Services/MetaSEO.php

namespace App\Services;

use App\Models\Appearance;
use App\Models\Company;

class MetaSEO
{
    protected $title;
    protected $meta_title;
    protected $meta_desc;
    protected $meta_image;

    public function __construct()
    {
        try {
            $company = Appearance::where('type', 'profile')->first();
            $seo = $company->value['seo'];
            $this->title = $seo['meta_title'] ?? 'Vinawebapp.com';
            $this->meta_title = $seo['meta_title'] ?? 'Vinawebapp.com -Một Công Ty Thiết Kế Website Đi Trước Thời Đại Nhiều Năm Ánh Sáng';
            $this->meta_desc = $seo['meta_desc'] ?? 'Cuối cùng bạn đã đến. Sẵn sàng cho một trang web mới đẹp và hiệu quả? Chúng tôi,nhóm thiết kế web chuyên nghiệp của bạn, đang chờ bạn yêu cầu.';
            $this->meta_image = $seo['meta_image'];
        } catch (\Throwable $th) {
            $this->title = 'Vinawebapp.com';
            $this->meta_title = 'Vinawebapp.com -Một Công Ty Thiết Kế Website Đi Trước Thời Đại Nhiều Năm Ánh Sáng';
            $this->meta_desc = 'Cuối cùng bạn đã đến. Sẵn sàng cho một trang web mới đẹp và hiệu quả? Chúng tôi,nhóm thiết kế web chuyên nghiệp của bạn, đang chờ bạn yêu cầu.';
            $this->meta_image = 'https://file.vinawebapp.com/uploads/images/Company/vnwaLogoIcon.png';
        }



    }

    public function setTitle($title = null)
    {
        if ($title)
            $this->title = $title;
        return $this;
    }

    public function setMetaTitle($meta_title = null)
    {
        if ($meta_title)
            $this->meta_title = $meta_title;
        return $this;
    }

    public function setMetaDesc($meta_desc = null)
    {
        if ($meta_desc)
            $this->meta_desc = $meta_desc;
        return $this;
    }

    public function setMetaImage($meta_image = null)
    {
        if ($meta_image)
            $this->meta_image = $meta_image;
        return $this;
    }

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
