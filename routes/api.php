<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ItemController;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/items', [ItemController::class, 'index']);
Route::get('/item', [ItemController::class, 'fetchItem']);
Route::post('/add-item', [ItemController::class, 'addItem']);