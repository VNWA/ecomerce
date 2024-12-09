<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;



    public function formatPhone($phone)
    {
        return substr($phone, 0, 2) . '****' . substr($phone, -2);
    }
    public function formatEmail($email)
    {
        $parts = explode('@', $email);

        if (count($parts) === 2) {
            $localPart = $parts[0];
            $domainPart = $parts[1];

            // Chỉ giữ 2 ký tự đầu của local part, còn lại thay bằng '*'
            $formattedLocalPart = substr($localPart, 0, 2) . str_repeat('*', max(0, strlen($localPart) - 2));

            return $formattedLocalPart . '@' . $domainPart;
        }

        return $email; // Trả về email gốc nếu không hợp lệ
    }
}
