<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\item;
use Illuminate\Http\Request;

class ItemController extends Controller
{    
    /**
     * Return all items
     *
     * @return void
     */
    public function index()
    {
        return response()->json([
            'status' => true,
            'message' => 'items fetched',
            'data' => item::all()
        ], 200);
    }

    public function fetchItem(Request $request)
    {
        $request->validate([
            'item_id' => 'required|integer'
        ]);
        $item = item::find($request->item_id);
        if($item){
            return response()->json([
                'status' => true,
                'message' => 'items fetched',
                'data' => $item->first()
            ], 200);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'item not found',
                'data' => []
            ], 200);
        }
    }
}
