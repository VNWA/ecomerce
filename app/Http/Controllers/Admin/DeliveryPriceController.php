<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\DeliveryPrice;
use Illuminate\Http\Request;

class DeliveryPriceController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'delivery_id' => 'required|integer',
            'delivery_country_id' => 'required|integer',
            'price' => 'required|integer',
        ]);

        // Kiểm tra xem bản ghi đã tồn tại chưa
        $exists = DeliveryPrice::where('delivery_id', $request->delivery_id)
            ->where('delivery_country_id', $request->delivery_country_id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Delivery price for this delivery and country already exists.'], 400);
        }

        $data = $request->only(['delivery_id', 'delivery_country_id', 'price']);

        try {
            DeliveryPrice::create($data);
            return response()->json(['message' => 'Upload Delivery Success'], 200);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'delivery_id' => 'required|integer',
            'delivery_country_id' => 'required|integer',
            'price' => 'required|integer',
        ]);

        // Kiểm tra xem bản ghi đã tồn tại chưa
        $exists = DeliveryPrice::where('delivery_id', $request->delivery_id)
            ->where('delivery_country_id', $request->delivery_country_id)
            ->where('id', '!=', $id) // Đảm bảo không kiểm tra chính nó
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Delivery price for this delivery and country already exists.'],  400);
        }

        $data = $request->only(['delivery_id', 'delivery_country_id', 'price']);

        try {
            $deliveryPrice = DeliveryPrice::findOrFail($id);
            $deliveryPrice->update($data);
            return response()->json(['message' => 'Update Delivery Country Success'], 200);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function delete($id)
    {
        try {
            $deliveryPrice = DeliveryPrice::findOrFail($id);
            $deliveryPrice->delete();
            return response()->json(['message' => 'Delete Delivery Price success'], 200);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
