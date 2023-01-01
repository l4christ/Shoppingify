<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\ListController;

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/items', [ItemController::class, 'index']);
    Route::get('/item', [ItemController::class, 'fetchItem']);
    Route::post('/add-item', [ItemController::class, 'addItem']);
    Route::post('/new-list', [ListController::class, 'newList']);
});