<?php

namespace App\Http\Middleware;

use App\Models\Locale;
use App\Models\Setting;
use Config;
use DB;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */


    public function share(Request $request): array
    {

        $defaultConnection = 'es_db';
        $connection = session('db_connection', $defaultConnection);
        Config::set('database.default', $connection);
        $setting = Setting::where('type', 'frontend_urls')->first();
        $frontend_url = '';
        if ($setting) {
            $urls = $setting->json_value;
            $frontend_url = $urls[0] ?? '';
        }
        return array_merge(parent::share($request), [
            'frontend_url' => $frontend_url,
            'current_db' => $connection,
        ]);
    }
}
