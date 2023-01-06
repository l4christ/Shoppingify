<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemCategory;
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
            'data' => Item::all()
        ], 200);
    }

    public function fetchItem(Request $request)
    {
        $request->validate([
            'item_id' => 'required|integer'
        ]);
        $item = Item::find($request->item_id);
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
            ], 404);
        }
    }

    public function addItem(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'category_id' => 'required|exists:item_categories,id',
            'note' => 'nullable',
        ]);

        $item = Item::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'note' => $request->name,
        ]);
        if($item){
            return response()->json([
                'status' => true,
                'message' => 'item created',
                'data' => []
            ], 200);
        }
    }

    public  function addCategory(Request $request){
        $request->validate([
            'name' => 'required|string'
        ]);

        $category = new ItemCategory();
        $category->name = $request->name;

        if($category->save()){
            return response()->json([
                'status' => true,
                'message' => 'category added',
                'data' => []
            ], 200);
        }
    }


    public  function fetchCategories()
    {
        return response()->json([
            'status' => true,
            'message' => 'category added',
            'data' => ItemCategory::select(['id', 'name'])->get()
        ], 200);
    }
}
