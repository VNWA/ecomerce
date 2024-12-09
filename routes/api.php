<?php

use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Client\CustomerController;
use App\Http\Controllers\Client\NewsletterController;
use App\Http\Controllers\Client\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::post('/v1/order/stripe-webhook', [OrderController::class, 'stripeWebhook']);

Route::prefix('{lang?}')
    ->middleware(['vnwa.api'])->group(function () {



    Route::prefix('v1')->group(function () {
        Route::get('/test/{code}', [OrderController::class, 'test']);

        Route::get('/load-data-home-page', [ClientController::class, 'loadDataHomePage']);
        Route::get('/get-data-layout', [ClientController::class, 'loadDataLayout']);
        Route::get('/get-data-static-page/{slug}', [ClientController::class, 'loadDataStaticPage']);
        Route::get('/check-product/{sku}', [ClientController::class, 'checkProductSku']);
        Route::get('/get-detail-product/{slug}', [ClientController::class, 'loadDataDetailProduct']);
        Route::get('/get-data-delivery-countries', [ClientController::class, 'loadDataDeliveryCountries']);
        Route::get('/load-data-products', [ClientController::class, 'loadProducts']);
        Route::get('/load-data-products-by-color-page', [ClientController::class, 'loadProductsColorPages']);
        Route::get('/load-data-search-products/{query}', [ClientController::class, 'loadSearchProducts']);

        Route::get('/load-category-page/{slug}', [ClientController::class, 'loadDataCategoryPage']);
        Route::get('/load-products-by-catgory-page/{slug}', [ClientController::class, 'loadProductsByCategory']);
        Route::get('/check-coupon/{code}', [ClientController::class, 'findCoupon']);

        Route::post('/subscribe',  [NewsletterController::class, 'subscribe'])->middleware('throttle.json:5,1');
        Route::post('/register', [CustomerController::class, 'register'])->middleware('throttle.json:5,1');
        Route::post('/login', [CustomerController::class, 'login'])->middleware('throttle.json:5,1');
        Route::post('/send-otp', [CustomerController::class, 'sendOTP'])->middleware('throttle.json:1,1');
        Route::post('/verify-otp', [CustomerController::class, 'verifyOtp'])->middleware('throttle.json:5,1');

        Route::post('/password/email', [CustomerController::class, 'sendResetLinkEmail'])->middleware('throttle.json:5,1');
        Route::post('/password/reset', [CustomerController::class, 'resetPassword']);

        Route::prefix('order')->group(function () {
            Route::get('/payment-success/{status}/{orderCode}', [OrderController::class, 'paymentSuccess'])->name('api.order.payment.redirect');

            route::get('/detail/{code}', [OrderController::class, 'detail']);
            route::get('/order-items/{code}', [OrderController::class, 'orderItems']);
            route::post('/payment/{code}', [OrderController::class, 'payment']);
            Route::post('/create', [OrderController::class, 'create']);


        });


        Route::middleware(['auth:sanctum'])->group(function () {
            Route::prefix('customer')->group(function () {
                Route::get('/', function (Request $request) {
                    return response()->json($request->user());
                });
                Route::post('/update-profile', [CustomerController::class, 'updateProfile']);
                Route::post('/update-password', [CustomerController::class, 'updatePassword']);
                Route::get('/orders', [CustomerController::class, 'customerOrders']);
            });
        });


    });

})->where('lang', 'en|es');
