<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ItemList;
use App\Models\ItemListPivot;
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
            'user_id' => auth()->id(),
            'state' => 0
        ]);

        if ($list) {
            return response()->json([
                'status' => true,
                'message' => $list->name . ', created',
                'data' => []
            ], 201);
        }
        return response()->json([
            'status' => false,
            'message' => 'Unable to create list',
            'data' => []
        ]);
    }

    public function addItemToList(Request $request)
    {
        $request->validate([
            'item_id' => 'required|integer|exists:items,id',
            'list_id' => 'required|integer|exists:item_lists,id',
            'item_category_id' => 'required|integer|exists:item_categories,id',
            'quantity' => 'required|integer',
        ]);

        $item_list = new ItemListPivot();
        $item_list->item_id = $request->item_id;
        $item_list->list_id = $request->list_id;
        $item_list->item_categories_id = $request->item_category_id;
        $item_list->qty = $request->quantity;

        if ($item_list->save()) {
            return response()->json([
                'status' => true,
                'message' => 'item added',
                'data' => []
            ], 201);
        }
        return response()->json([
            'status' => false,
            'message' => 'Unable to add item',
            'data' => []
        ]);
    }

    public function fetchListItems(Request $request)
    {
        $request->validate([
            'list_id' => 'required|integer|exists:item_lists,id',
        ]);
        $list = ItemList::find($request->list_id);

        if ($list->owner->id ?? 0 == auth()->id()) {
            return response()->json([
                'status' => true,
                'message' => 'items returned',
                'data' => $list->listitems,
            ], 200);
        }
        return response()->json([
            'status' => false,
            'message' => 'invalid user',
            'data' => [],
        ], 200);
    }

    public function removeItemFromList(Request $request)
    {
        $request->validate([
            'list_item_id' => 'required|integer|exists:item_list_pivots,id',
        ]);
        $item_list = ItemListPivot::find($request->list_item_id);
        if ($item_list->user_id != auth()->id()) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid user',
                'data' => []
            ]);
        }
        if ($item_list->delete()) {
            return response()->json([
                'status' => true,
                'message' => 'Item removed',
                'data' => []
            ]);
        }
    }

    public function setDefaultList(Request $request)
    {
        $request->validate([
            'list_id' => 'required|integer'
        ]);
        $list = ItemList::find($request->list_id);
        if ($list->owner->id ?? 0 == auth()->id()) {
            ItemList::where('user_id', auth()->id())->update(['state' => false]);
            $list->state = true;
            $list->save();
            return response()->json([
                'status' => false,
                'message' => $list->name . ', set as default',
                'data' => []
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Invalid user',
            'data' => []
        ]);
    }

    public function fetchUserList()
    {
        return response()->json([
            'status' => false,
            'message' => 'List returned',
            'data' => auth()->user()->userlists
        ]);
    }

    public function deleteUserList(Request $request)
    {
        $request->validate([
            'list_id' => 'required|integer'
        ]);
        $list = ItemList::find($request->list_id);
        if ($list->owner->id ?? 0 == auth()->id()) {
            $list->delete();
            return response()->json([
                'status' => true,
                'message' => $list->name . ' deleted',
                'data' => []
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Invalid user',
            'data' => []
        ]);
    }
}
