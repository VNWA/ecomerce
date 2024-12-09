<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PayPal
{
    protected $paypalConfig;
    protected $cacheKey = 'paypal';

    public function __construct()
    {
        $this->loadConfiguration();
    }

    // Tải cấu hình PayPal từ cache hoặc database
    protected function loadConfiguration()
    {
        try {
            $this->paypalConfig = Cache::remember($this->cacheKey, 86400, function () {
                return $this->loadFromDatabase();
            });
        } catch (\Exception $e) {
            Log::error('PayPal configuration error: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function loadFromDatabase()
    {
        $setting = Setting::where('type', 'paypal')->first();

        if (!$setting) {
            throw new \Exception('PayPal configuration not found in database.');
        }

        $paypal = $setting->json_value; // Giả định json_value chứa cấu hình JSON
        return [
            'mode' => $paypal['mode'],
            $paypal['mode'] => [
                'client_id' => $paypal['client_id'],
                'client_secret' => $paypal['secret_key'],
                'app_id' => $paypal['app_id'] ?? null,
            ],
            'payment_action' => 'sale',
            'notify_url' => route('Paypal.Webhook'),
            'currency' => $paypal['currency'] ?? 'USD',
            'locale' => $paypal['locale'] ?? 'en_US',
            'validate_ssl' => true,
        ];
    }

    public function refreshCache()
    {
        Cache::forget($this->cacheKey);
        $this->loadConfiguration();
    }

    public function verifyPayment($orderID)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials($this->paypalConfig);

        try {
            $provider->getAccessToken();
            $response = $provider->showOrderDetails($orderID);

            if (empty($response['error'])) {
                if ($response['status'] === 'COMPLETED') {
                    return true;
                }
            }

            Log::warning('PayPal payment verification failed.', [
                'orderID' => $orderID,
                'response' => $response,
            ]);
            return true;

        } catch (\Exception $e) {
            Log::error('PayPal API error: ' . $e->getMessage(), [
                'orderID' => $orderID,
            ]);
            return false;
        }
    }


}
