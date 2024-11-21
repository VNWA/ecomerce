<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\DeliveryCountry;
use Illuminate\Http\Request;

class DeliveryCountryController extends Controller
{
    function create(Request $request)
    {

        $request->validate([
            'name' => 'required|string',
        ]);
        $data = [];
        $data['name'] = $request->name;
        try {
            DeliveryCountry::create($data);
            return response()->json(['message' => 'Uploads Delivery Success'], 200);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);

        }
    }
    function update(Request $request,$id)
    {

        $request->validate([
            'name' => 'required|string',
        ]);
        $data = [];
        $data['name'] = $request->name;
        try {
            DeliveryCountry::find($id)->update($data);
            return response()->json(['message' => 'Update Delivery Country Success'], 200);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);

        }
    }

    function delete($id)
    {
        try {
            DeliveryCountry::where('id', $id)->delete();
            return response()->json(['message' => 'Delete Delivery Country success'], 200);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
