<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaiKhoanController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('accounts')->group(function () {
    Route::get('/', [TaiKhoanController::class, 'index']);
    Route::post('/', [TaiKhoanController::class, 'store']);
    Route::put('/{id}', [TaiKhoanController::class, 'update']);
    Route::delete('/{id}', [TaiKhoanController::class, 'destroy']);
});