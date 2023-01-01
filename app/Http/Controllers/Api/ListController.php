<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ItemList;
use Illuminate\Http\Request;

class ListController extends Controller
{
    public function newList(Request $request)
    {
        $request->validate([
            'name' => 'required|string'
        ]);

        $list = ItemList::create([
            'name' => $request->name,
            'user_id' => auth()->id()
        ]);

        if ($list) {
            return response()->json([
                'status' => true,
                'message' => 'list created',
                'data' => []
            ], 201);
        }
    }
}
