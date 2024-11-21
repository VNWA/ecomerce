<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function loadDataTable()
    {
        $orders = Contact::latest()->get();
        return response()->json($orders, 200);
    }
    function delete(Request $request)
    {

        try {
            Contact::whereIn('id', $request->dataId)->delete();
            return response()->json(['message' => 'Delete data success'], 200);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
