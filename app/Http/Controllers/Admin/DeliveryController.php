<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Delivery;
use App\Models\DeliveryCountry;
use App\Models\DeliveryPrice;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function loadDataTable()
    {
        $deliveries = Delivery::latest()->get();
        $deliveryCountries = DeliveryCountry::latest()->get();
        $deliveryPrices = DeliveryPrice::with(['delivery:id,name', 'delivery_country:id,name'])->latest()->get();
        return response()->json(['deliveries' => $deliveries, 'deliveryCountries' => $deliveryCountries, 'deliveryPrices' => $deliveryPrices], 200);
    }

    function create(Request $request)
    {

        $request->validate([
            'name' => 'required|string',
            'image' => 'required|string',
        ]);
        $data = [];
        $data['name'] = $request->name;
        $data['image'] = $request->image;
        try {
            Delivery::create($data);
            return response()->json(['message' => 'Update Delivery Success'], 200);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);

        }
    }
    function update(Request $request, $id)
    {

        $request->validate([
            'name' => 'required|string',
            'image' => 'required|string',
        ]);
        $data = [];
        $data['name'] = $request->name;
        $data['image'] = $request->image;
        try {
            Delivery::find($id)->update($data);
            return response()->json(['message' => 'Uploads Delivery Success'], 200);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);

        }
    }

    function delete($id)
    {
        try {
            Delivery::where('id', $id)->delete();
            return response()->json(['message' => 'Delete data success'], 200);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
