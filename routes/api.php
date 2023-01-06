<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\ListController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/fetch-categories', [ItemController::class, 'fetchCategories']);
    Route::post('/fetch-list', [ListController::class, 'fetchSelectedList']);
    Route::post('/update-list-item-qty', [ListController::class, 'updateListItemQuantity']);
    Route::post('/mark-as-complete', [ListController::class, 'markAsComplete']);

    Route::get('/items', [ItemController::class, 'index']);
    Route::get('/item', [ItemController::class, 'fetchItem']);
    Route::get('/fetch-user-lists', [ListController::class, 'fetchUserList']);
    Route::delete('/delete-user-list', [ListController::class, 'deleteUserList']);
    Route::post('/add-item', [ItemController::class, 'addItem']);
    Route::post('/add-category', [ItemController::class, 'addCategory']);
    Route::post('/new-list', [ListController::class, 'newList']);
    Route::post('/add-item-to-list', [ListController::class, 'addItemToList']);
    Route::post('/set-default-list', [ListController::class, 'setDefaultList']);
    Route::get('/fetch-list-items', [ListController::class, 'fetchListItems']);
    Route::delete('/remove-list-item', [ListController::class, 'removeItemFromList']);
});

// Fetch category
// Fetch selected list
// Update list item quantity
// Mark list as complete