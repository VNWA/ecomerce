<?php

use App\Http\Controllers\Admin\AppearanceController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\BlogPostController;
use App\Http\Controllers\Admin\BlogTagController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\ColorController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\DeliveryController;
use App\Http\Controllers\Admin\DeliveryCountryController;
use App\Http\Controllers\Admin\DeliveryPriceController;
use App\Http\Controllers\Admin\Inertia\ApiTokenController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductCategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductExcelController;
use App\Http\Controllers\Admin\ProductImportController;
use App\Http\Controllers\Admin\ProductOrderController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\StaticPageController;
use App\Http\Controllers\Admin\UrlController;
use App\Http\Controllers\Admin\VinawebappController;
use App\Models\Appearance;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Admin\MediaController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::post('/stripe-payment-status', [OrderController::class, 'stripeWebhook'])->name('Stripe.Webhook');
Route::post('/paypal-payment-status', [OrderController::class, 'stripeWebhook'])->name('Paypal.Webhook');

Route::get('/', function () {
    return Inertia::render('Admin/Welcome');
});
Route::get('vnwa/', function () {
    return Inertia::render('Admin/Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});
Route::get('/vnwa/login', function () {
    return Inertia::render('Admin/Auth/Login');
})->name('login');

Route::prefix('vnwa')
    ->middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])
    ->group(function () {

        Route::prefix('/configs')->group(function () {
            Route::post('/change-database', [VinawebappController::class, 'changeDatabase'])->name('Config.ChangeDatabase');
            Route::get('/', [VinawebappController::class, 'showConfig'])->name('Config.Show');
            Route::post('/update', [VinawebappController::class, 'createBackup'])->name('Config.Update');
        });
        Route::prefix('/settings')->group(function () {
            Route::get('/', [SettingController::class, 'showAll'])->name('Setting');
            Route::prefix('/security')->group(function () {
                Route::get('/frontend-urls', [SettingController::class, 'showFrontendUrls'])->name('Setting.Security.FrontendUrls');
                Route::post('/frontend-urls', [SettingController::class, 'updateFrontendUrls'])->name('Setting.Security.FrontendUrls.Update');
            });

            Route::prefix('/configs')->group(function () {
                Route::get('/stripe', [SettingController::class, 'showConfigStripe'])->name('Setting.Config.Stripe');
                Route::post('/stripe', [SettingController::class, 'updateConfigStripe'])->name('Setting.Config.Stripe.Update');

                Route::get('/paypal', [SettingController::class, 'showConfigPaypal'])->name('Setting.Config.Paypal');
                Route::post('/paypal', [SettingController::class, 'updateConfigPaypal'])->name('Setting.Config.Paypal.Update');


            });



        });



        Route::prefix('/vinawebapp-cms')->group(function () {
            Route::get('/backup', [VinawebappController::class, 'createBackup'])->name('VNWA.Backup');
            Route::get('/database-current-connect', [VinawebappController::class, 'createBackup'])->name('VNWA.DbInfo');

        });
        Route::prefix('media')->group(function () {
            Route::get('/popup', function () {
                return Inertia::render('Admin/MediaPopup');
            })->name('Media.Popup');

            Route::get('/', function () {
                return Inertia::render('Admin/Media');
            })->name('Media');

            Route::post('get-data-files', [MediaController::class, 'getDataFiles']);

            Route::post('upload-files', [MediaController::class, 'uploadFiles']);
            Route::post('create-directory', [MediaController::class, 'createDirectory']);
            Route::post('rename', [MediaController::class, 'rename']);
            Route::post('delete', [MediaController::class, 'delete']);
        });
        Route::prefix('ui-block-popup')->group(function () {
            Route::get('/hero-section-config', function () {
                return Inertia::render('Admin/UiBlock/HeroSection');
            })->name('UiBlock.HeroSection');
            Route::get('/step-slides-config', function () {
                return Inertia::render('Admin/UiBlock/StepSlide');
            })->name('UiBlock.StepSlide');
            Route::get('/youtube-videos-config', function () {
                return Inertia::render('Admin/UiBlock/YoutubeVideos');
            })->name('UiBlock.YoutubeVideos');


        });

        Route::post('/change-status', [VinawebappController::class, 'changeStatus']);
        Route::post('/change-highlight', [VinawebappController::class, 'changeHighlight']);
        Route::post('/change-ord', [VinawebappController::class, 'changeORD']);

        Route::prefix('contacts')->group(function () {
            Route::get('/load-data-table', [ContactController::class, 'loadDataTable']);
            Route::get('/', function () {
                return Inertia::render('Admin/Contact');
            })->name('Contact');
            Route::post('/delete', [ContactController::class, 'delete']);
        });

        Route::get('/check-slug/{slug}/{model_type?}/{model_id?}', [UrlController::class, 'checkSlug']);

        Route::get('/dashboard', function () {
            return Inertia::render('Admin/Dashboard');
        })->name('dashboard');
        Route::get('/', function () {
            return Inertia::render('Admin/Dashboard');
        });

        Route::prefix('blog')
            ->group(function () {

                Route::prefix('categories')->group(function () {
                    Route::get('/', [BlogCategoryController::class, 'index'])->name('Blog.Categories');
                    Route::get('/load-new-tree-data', [BlogCategoryController::class, 'loadNewDataTree']);

                    Route::post('/update-tree', [BlogCategoryController::class, 'updateTree']);
                    Route::get('/get-detail-category/{id}', [BlogCategoryController::class, 'getDetailCategory']);
                    Route::post('/create', [BlogCategoryController::class, 'create']);
                    Route::post('/update/{id}', [BlogCategoryController::class, 'update']);
                    Route::post('/delete/{id}', [BlogCategoryController::class, 'delete']);

                });
                Route::prefix('tags')->group(function () {
                    Route::get('/load-data-table', [BlogTagController::class, 'loadDataTable']);
                    Route::get('/', function () {
                        return Inertia::render('Admin/Blog/BlogTags');
                    })->name('Blog.Tags');
                    Route::post('/create', [BlogTagController::class, 'create']);
                    Route::post('/update/{id}', [BlogTagController::class, 'update']);
                    Route::post('/delete', [BlogTagController::class, 'delete']);
                });
                Route::prefix('posts')->group(function () {
                    Route::get('/', function () {
                        return Inertia::render('Admin/Blog/BlogPost/Show');
                    })->name('Blog.Posts');
                    Route::get('/load-data-table', [BlogPostController::class, 'loadDataTable']);
                    Route::get('/load-data-categories-tree-and-tags', [BlogPostController::class, 'loadDataCategoriesTreeAndTags']);
                    Route::get('/load-data-categories-and-tags', [BlogPostController::class, 'loadDataCategoriesAndTags']);
                    Route::get('/create', function () {
                        return Inertia::render('Admin/Blog/BlogPost/Create');
                    })->name('Blog.Posts.Create');
                    Route::post('/create', [BlogPostController::class, 'create']);
                    Route::get('/edit/{id}', [BlogPostController::class, 'showEdit'])->name('Blog.Posts.Edit');
                    Route::post('/update/{id}', [BlogPostController::class, 'update']);
                    Route::post('/delete', [BlogPostController::class, 'delete']);
                });
            });
        Route::prefix('ecommerce')
            ->group(function () {
                Route::prefix('product-categories')->group(function () {
                    Route::get('/', [ProductCategoryController::class, 'index'])->name('Ecommerce.ProductCategories');
                    Route::get('/load-new-tree-data', [ProductCategoryController::class, 'loadNewDataTree']);
                    Route::post('/update-tree', [ProductCategoryController::class, 'updateTree']);
                    Route::get('/get-detail-category/{id}', [ProductCategoryController::class, 'getDetailCategory']);
                    Route::post('/create', [ProductCategoryController::class, 'create']);
                    Route::post('/update/{id}', [ProductCategoryController::class, 'update']);
                    Route::post('/delete/{id}', [ProductCategoryController::class, 'delete']);
                });

                Route::prefix('brands')->group(function () {
                    Route::get('', [BrandController::class, 'showIndex'])->name('Ecommerce.Brand');
                    Route::post('load-data-table', [BrandController::class, 'loadDataTable']);
                    Route::get('/create', function () {
                        return Inertia::render('Admin/Ecommerce/Brand/Create');
                    })->name('Ecommerce.Brand.Create');

                    Route::post('/delete', [BrandController::class, 'delete']);
                    Route::post('/create', [BrandController::class, 'create']);

                    Route::get('/edit/{id}', [BrandController::class, 'showEdit'])->name('Ecommerce.Brand.Edit');
                    Route::post('/update/{id}', [BrandController::class, 'update']);
                });





                Route::prefix('coupons')->group(function () {
                    Route::get('/', [CouponController::class, 'index'])->name('Ecommerce.Coupon');
                    Route::get('load-data-table', [CouponController::class, 'loadDataTable'])->name('Ecommerce.Coupon.LoadData');
                    Route::get('/create', function () {
                        return Inertia::render('Admin/Ecommerce/Coupon/Create');
                    })->name('Ecommerce.Coupon.Create');

                    Route::post('/delete', [CouponController::class, 'delete'])->name('Ecommerce.Coupon.Delete');
                    Route::post('/create', [CouponController::class, 'store'])->name('Ecommerce.Coupon.Store');

                    Route::get('/edit/{id}', [CouponController::class, 'edit'])->name('Ecommerce.Coupon.Edit');
                    Route::post('/update/{id}', [CouponController::class, 'update'])->name('Ecommerce.Coupon.Update');
                });


                Route::prefix('colors')->group(function () {
                    Route::get('/', [ColorController::class, 'showIndex'])->name('Ecommerce.Color');
                    Route::post('load-data-table', [ColorController::class, 'loadDataTable']);
                    Route::get('/create', function () {
                        return Inertia::render('Admin/Ecommerce/Color/Create');
                    })->name('Ecommerce.Color.Create');

                    Route::post('/delete', [ColorController::class, 'delete']);
                    Route::post('/create', [ColorController::class, 'create']);

                    Route::get('/edit/{id}', [ColorController::class, 'showEdit'])->name('Ecommerce.Color.Edit');
                    Route::post('/update/{id}', [ColorController::class, 'update']);
                });




                Route::prefix('deliveries')->group(function () {
                    Route::get('/', function () {
                        return Inertia::render('Admin/Ecommerce/Deliveries');
                    })->name('Ecommerce.Delivery');
                    Route::get('/load-data-table', [DeliveryController::class, 'loadDataTable'])->name('Ecommerce.Delivery.Data');
                    Route::post('/create', [DeliveryController::class, 'create'])->name('Ecommerce.Delivery.Store');
                    Route::post('/update/{id}', [DeliveryController::class, 'update'])->name('Ecommerce.Delivery.Update');
                    Route::post('/delete/{id}', [DeliveryController::class, 'delete'])->name('Ecommerce.Delivery.Delete');

                    Route::prefix('country')->group(function () {
                        Route::post('/create', [DeliveryCountryController::class, 'create'])->name('Ecommerce.Delivery.Country.Store');
                        Route::post('/update/{id}', [DeliveryCountryController::class, 'update'])->name('Ecommerce.Delivery.Country.Update');
                        Route::post('/delete/{id}', [DeliveryCountryController::class, 'delete'])->name('Ecommerce.Delivery.Country.Delete');
                    });
                    Route::prefix('price')->group(function () {
                        Route::post('/create', [DeliveryPriceController::class, 'create'])->name('Ecommerce.Delivery.Price.Store');
                        Route::post('/update/{id}', [DeliveryPriceController::class, 'update'])->name('Ecommerce.Delivery.Price.Update');
                        Route::post('/delete/{id}', [DeliveryPriceController::class, 'delete'])->name('Ecommerce.Delivery.Price.Delete');

                    });
                });




                Route::prefix('products')->group(function () {
                    Route::post('/change-is_seller/{id}', [ProductController::class, 'changeIsSeller'])->name('Ecommerce.Product.ChangeIsSeller');
                    Route::get('/get-mini-products', [ProductController::class, 'loadMiniProducts'])->name('Ecommerce.Product.MiniProducts');

                    Route::get('/', function () {
                        return Inertia::render('Admin/Ecommerce/Product/Show');
                    })->name('Ecommerce.Product');

                    Route::prefix('excel')->group(function () {
                        Route::get('/import', function () {
                            return Inertia::render('Admin/Ecommerce/Product/Import');
                        })->name('Ecommerce.Product.Import');
                        Route::get('/convert', function () {
                            return Inertia::render('Admin/Ecommerce/Product/Convert');
                        })->name('Ecommerce.Product.Convert');
                        Route::get('/export-template', [ProductExcelController::class, 'export'])->name('Ecommerce.Product.ExportTemplate');
                        Route::post('/read', [ProductExcelController::class, 'read']);
                        Route::post('/import', [ProductExcelController::class, 'import']);
                    });

                    Route::get('/load-data-table', [ProductController::class, 'loadDataTable']);
                    Route::get('/load-data-categories-tree-and-brands-and-colors', [ProductController::class, 'loadDataCategoriesTreeAndBrandsAndColors']);
                    Route::get('/load-data-categories-and-brands', [ProductController::class, 'loadDataCategoriesAndBrands']);

                    Route::get('/create', function () {
                        return Inertia::render('Admin/Ecommerce/Product/Create');
                    })->name('Ecommerce.Product.Create');

                    Route::post('/create', [ProductController::class, 'create']);
                    Route::get('/edit/{id}', [ProductController::class, 'showEdit'])->name('Ecommerce.Product.Edit');
                    Route::post('/update/{id}', [ProductController::class, 'update']);
                    Route::post('/delete', [ProductController::class, 'delete']);
                });


                Route::prefix('orders')->group(function () {
                    Route::get('/', [OrderController::class, 'index'])->name('Ecommerce.Order');
                    Route::get('/find-products/{s}', [OrderController::class, 'findProducts'])->name('Ecommerce.Order.FindProducts');
                    Route::get('load-order-data/{code}', [OrderController::class, 'loadOrderData'])->name('Ecommerce.Order.Data');
                    Route::get('load-data-table', [OrderController::class, 'loadDataTable'])->name('Ecommerce.Order.LoadData');
                    Route::get('load-orders-form-fileds', [OrderController::class, 'loadOrderFormFileds'])->name('Ecommerce.Order.loadOrderFormFileds');

                    Route::get('/create', [OrderController::class, 'viewCreate'])->name('Ecommerce.Order.Create');
                    Route::post('/create', [OrderController::class, 'store'])->name('Ecommerce.Order.Store');

                    Route::get('/copy/{id}', [OrderController::class, 'viewCopy'])->name('Ecommerce.Order.Copy');

                    Route::post('/delete', [OrderController::class, 'delete'])->name('Ecommerce.Order.Delete');

                    Route::get('/edit/{id}', [OrderController::class, 'viewEdit'])->name('Ecommerce.Order.Edit');
                    Route::post('/update/{id}/{type}', [OrderController::class, 'update'])->name('Ecommerce.Order.Update');
                });

            });
        Route::prefix('banners')->group(function () {
            Route::get('', action: [BannerController::class, 'showIndex'])->name('Banner');
            Route::post('load-data-table', [BannerController::class, 'loadDataTable']);
            Route::get('/create', function () {
                return Inertia::render('Admin/Banner/Create');
            })->name('Banner.Create');

            Route::post('/delete', [BannerController::class, 'delete'])->name('Banner.Delete');
            Route::post('/create', [BannerController::class, 'create']);

            Route::get('/edit/{id}', [BannerController::class, 'showEdit'])->name('Banner.Edit');
            Route::post('/update/{id}', [BannerController::class, 'update']);
        });
        Route::prefix('static-pages')->group(function () {
            Route::get('', action: [StaticPageController::class, 'showIndex'])->name('StaticPage');
            Route::post('load-data-table', [StaticPageController::class, 'loadDataTable']);
            Route::post('change-is-header/{id}', [StaticPageController::class, 'changeIsHeader'])->name('StaticPage.ChangeIsHeader');
            Route::post('change-is-header-main/{id}', [StaticPageController::class, 'changeIsHeaderMain'])->name('StaticPage.ChangeIsHeaderMain');


            Route::get('/create', function () {
                return Inertia::render('Admin/StaticPage/Create');
            })->name('StaticPage.Create');

            Route::post('/delete', [StaticPageController::class, 'delete']);
            Route::post('/create', [StaticPageController::class, 'create']);

            Route::get('/edit/{id}', [StaticPageController::class, 'showEdit'])->name('StaticPage.Edit');
            Route::post('/update/{id}', [StaticPageController::class, 'update'])->name('StaticPage.Update');
        });
        Route::prefix('appearance')
            ->group(function () {
                Route::get('/', function () {
                    return Inertia::render('Admin/Appearance/Show');
                })->name('Appearance');
                Route::prefix('top-nav')->group(function () {
                    Route::get('/', function () {
                        return Inertia::render('Admin/Appearance/TopNav');
                    })->name('Appearance.TopNav');

                    Route::get('/load-json-data', [AppearanceController::class, 'loadJsonDataTopNav']);
                    Route::post('/update', [AppearanceController::class, 'updateTopNav']);
                });

                Route::prefix('profile')->group(function () {
                    Route::get('/', function () {
                        return Inertia::render('Admin/Appearance/Profile');
                    })->name('Appearance.Profile');

                    Route::get('/load-json-data', [AppearanceController::class, 'loadJsonDataProfile']);
                    Route::post('/update', [AppearanceController::class, 'updateProfile']);
                });

                Route::prefix('bot-search')->group(function () {
                    Route::get('/', function () {
                        return Inertia::render('Admin/Appearance/BotSearch');
                    })->name('Appearance.BotSearch');

                    Route::get('/load-json-data', [AppearanceController::class, 'loadJsonDataBotSearch']);
                    Route::post('/update', [AppearanceController::class, 'updateBotSearch']);
                });
                Route::prefix('logo')->group(function () {
                    Route::get('/', function () {
                        return Inertia::render('Admin/Appearance/Logo');
                    })->name('Appearance.Logo');

                    Route::get('/load-json-data', [AppearanceController::class, 'loadJsonDataLogo']);
                    Route::post('/update', [AppearanceController::class, 'updateLogo']);
                });

                Route::prefix('footer')->group(function () {
                    Route::get('/', function () {
                        return Inertia::render('Admin/Appearance/Footer');
                    })->name('Appearance.Footer');

                    Route::get('/load-json-data', [AppearanceController::class, 'loadJsonDataFooter']);
                    Route::post('/update', [AppearanceController::class, 'updateFooter']);
                });
                Route::prefix('home')->group(function () {
                    Route::get('/', function () {
                        return Inertia::render('Admin/Appearance/Home');
                    })->name('Appearance.Home');

                    Route::get('/load-json-data', [AppearanceController::class, 'loadJsonDataHome'])->name('Appearance.Home.LoadData');
                    Route::post('/update', [AppearanceController::class, 'updateHome'])->name('Appearance.Home.Update');
                });

                Route::prefix('about')->group(function () {
                    Route::get('/', function () {
                        return Inertia::render('Admin/Appearance/About');
                    })->name('Appearance.About');

                    Route::get('/load-json-data', [AppearanceController::class, 'loadJsonDataAbout']);
                    Route::post('/update', [AppearanceController::class, 'updateAbout']);
                });
            });

    });



