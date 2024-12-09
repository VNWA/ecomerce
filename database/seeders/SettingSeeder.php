<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::truncate();
        Setting::create([
            'type' => 'frontend_urls',
            'json_value' => [
                'http://localhost:3000',
            ],
        ]);
        Setting::create([
            'type' => 'stripe',
            'json_value' => [
                'secret_key' => '',
                'publish_key' => '',
                'webhook_key' => '',
            ],
        ]);
        Setting::create([
            'type' => 'paypal',
            'json_value' => [
                'client_id' => '',
                'client_secret' => '',
                'mode' => '',
                'app_id' => '',
                'notify_url' => '',
                'payment_action' => '',
                'currency' => '',
                'locale' => '',
                'validate_ssl' => true,

            ],
        ]);











    }
}
