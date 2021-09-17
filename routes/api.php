<?php

use App\Http\Controllers\FilterController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('auth:sanctum')->get('/test', function(Request $request) {
    return $request->user();
});
Route::prefix('offices')->group(function() {
    Route::get('/', [OfficeController::class, 'index']);
    Route::get('/addresses', [OfficeController::class, 'addresses']);
    Route::post('/store', [OfficeController::class, 'store']);
    Route::put('{office}', [OfficeController::class, 'update']);
    Route::delete('/{office}', [OfficeController::class, 'destroy']);
});

Route::prefix('users')->group(function() {
    Route::get('/', [UserController::class, 'index']);
    Route::post('/store', [UserController::class, 'store']);
    Route::put('/{user}', [UserController::class, 'update']);
    Route::delete('/{user}', [UserController::class, 'destroy']);
});

Route::prefix('filters')->group(function() {
    Route::get('/', [FilterController::class, 'index']);
    Route::post('/store', [FilterController::class, 'store']);
    Route::put('/{filter}', [FilterController::class, 'update']);
    Route::delete('/{filter}', [FilterController::class, 'destroy']);
});

Route::prefix('charts')->group(function() {
    Route::get('/{office}', [FilterController::class, 'index']);
    Route::post('/store', [FilterController::class, 'store']);
    Route::put('/{filter}', [FilterController::class, 'update']);
    Route::delete('/{filter}', [FilterController::class, 'destroy']);
});

