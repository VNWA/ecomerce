<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MetaSEO;
use Illuminate\Http\Request;

use App\Models\Appearance;
use App\Models\Product;
class AppearanceController extends Controller
{
    protected $metaSEO;

    public function __construct(MetaSEO $metaSEO)
    {
        $this->metaSEO = $metaSEO;
    }

    public function loadJsonDataHome()
    {

        $data = Appearance::where('type', 'homeSections')->first();
        $products = Product::where('is_show', 1)->get(['id', 'name', 'price', 'slug', 'images']);

        $transformedProducts = $products->map(function ($product) {
            $image = $product->images;
            return [
                'value' => $product->name . "_" . $product->slug . "_" . $product->price . "_" . $product->price_new . "_" . $image[0],
                'label' => $product->name,
            ];
        });
        return response()->json(['data' => $data->value, 'products' => $transformedProducts], 200);
    }
    function updateHome(Request $request)
    {
        try {
            Appearance::where('type', 'homeSections')->update(['value' => $request->all()]);
            return response()->json(['message' => 'Update Home Section Success'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Update Home Section Failing'], 500);

        }
    }

    public function loadJsonDataTopNav()
    {

        $data = Appearance::where('type', 'top_nav')->first();

        return response()->json($data->value, 200);
    }
    function updateTopNav(Request $request)
    {
        try {
            Appearance::where('type', 'top_nav')->update(['value' => $request->data]);
            return response()->json(['message' => 'Update Top Nav Success'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Update Top Nav Failing'], 500);

        }
    }


    public function loadJsonDataProfile()
    {

        $profile = Appearance::where('type', 'profile')->first();

        return response()->json($profile->value, 200);
    }
    function updateProfile(Request $request)
    {

        try {
            Appearance::where('type', 'profile')->update(['value' => $request->data]);
            $this->metaSEO->refreshCache();
            return response()->json(['message' => 'Update Porifie Company  Success'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Update Porifie Company  Failing'], 500);

        }
    }


    public function loadJsonDataBotSearch()
    {

        $data = Appearance::where('type', 'bot_search')->first();

        return response()->json($data->value, 200);
    }
    function updateBotSearch(Request $request)
    {
        try {
            Appearance::where('type', 'bot_search')->update(['value' => $request->data]);
            return response()->json(['message' => 'Update Bot Search Success'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Update Bot Search Failing'], 500);

        }
    }



    public function loadJsonDataLogo()
    {

        $data = Appearance::where('type', 'logo')->first();

        return response()->json($data->value, 200);
    }
    function updateLogo(Request $request)
    {
        try {
            Appearance::where('type', 'logo')->update(['value' => $request->data]);
            return response()->json(['message' => 'Update Logo Success'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Update Logo Failing'], 500);

        }
    }


    public function loadJsonDataFooter()
    {

        $data = Appearance::where('type', 'footer')->first();

        return response()->json($data->value, 200);
    }
    function updateFooter(Request $request)
    {
        try {
            Appearance::where('type', 'footer')->update(['value' => $request->data]);
            return response()->json(['message' => 'Update Footer Success'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Update Footer Failing'], 500);

        }
    }




    public function loadJsonDataAbout()
    {

        $data = Appearance::where('type', 'about')->first();

        return response()->json($data->value, 200);
    }
    function updateAbout(Request $request)
    {
        try {
            Appearance::where('type', 'about')->update(['value' => $request->data]);
            return response()->json(['message' => 'Update About Success'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Update About Failing'], 500);

        }
    }
}
