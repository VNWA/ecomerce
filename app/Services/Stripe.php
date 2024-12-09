<?php

namespace App\Services;

use App\Models\Appearance;
use App\Models\Setting;
use Config;
use Illuminate\Support\Facades\Cache;
use Stripe\StripeClient;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class Stripe
{
    protected $stripeConfig;
    protected $cacheKey = 'stripe';

    public function __construct()
    {
        // Lấy dữ liệu từ cache hoặc load từ database
        $stripeConfig = Cache::remember($this->cacheKey, 86400, function () {
            return $this->loadFromDatabase();
        });

        $this->stripeConfig = $stripeConfig;

    }

    // Load dữ liệu SEO từ database
    protected function loadFromDatabase()
    {

        $stripeConfig = Setting::where('type', 'stripe')->first();
        if (!$stripeConfig) {
            throw new \Exception('Stripe configuration not found in database.');
        }
        return $stripeConfig->json_value;
    }

    // Xóa cache và làm mới
    public function refreshCache()
    {
        Cache::forget($this->cacheKey); // Xóa cache cũ
        Cache::remember($this->cacheKey, 86400, function () {
            return $this->loadFromDatabase(); // Tải lại từ DB
        });
    }
    public function paymentLink($orderPrice, $orderCode, $origin, $currency = 'usd')
    {
        if (!isset($this->stripeConfig['secret_key'])) {
            throw new \Exception('Stripe secret key not configured.');
        }
        $currentDatabase = Config::get('database.default');

        $stripe = new StripeClient($this->stripeConfig['secret_key']);

        try {
            $price = $stripe->prices->create([
                'unit_amount' => $orderPrice,
                'currency' => $currency,
                'product_data' => [
                    'name' => $orderCode,
                ],
            ]);

            $paymentLink = $stripe->paymentLinks->create([
                'line_items' => [
                    [
                        'price' => $price->id,
                        'quantity' => 1,
                    ],
                ],
                'metadata' => [
                    'db' => $currentDatabase,
                    'order_code' => $orderCode,
                ],
                'after_completion' => [
                    'type' => 'redirect',
                    'redirect' => ['url' => $origin],
                ],
            ]);

            return $paymentLink->url;
        } catch (\Exception $e) {
            throw new \Exception('Failed to create payment link: ' . $e->getMessage());
        }
    }

    public function webhookEvent($payload, $sig_header)
    {
        if (!isset($this->stripeConfig['webhook_key'])) {
            throw new \Exception('Stripe webhook secret not configured.');
        }

        $endpoint_secret = $this->stripeConfig['webhook_key'];

        try {
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (\UnexpectedValueException $e) {
            throw new \Exception('Invalid payload.');
        } catch (SignatureVerificationException $e) {
            throw new \Exception('Invalid signature.');
        }

        return $event;
    }


}

