<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaiKhoanController;

// Route API cho sanctum (nếu sử dụng)
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// API chỉ dành cho admin
Route::middleware(['isLogin:admin'])->group(function () {
    // API quản lý tài khoản - chỉ admin mới có quyền
    Route::prefix('accounts')->group(function () {
        Route::get('/', [TaiKhoanController::class, 'index']);
        Route::post('/', [TaiKhoanController::class, 'store']);
        Route::put('/{id}', [TaiKhoanController::class, 'update']);
        Route::delete('/{id}', [TaiKhoanController::class, 'destroy']);
    });
    
    // Các API admin khác có thể thêm ở đây
});

// API dành cho đọc giả
Route::middleware(['isLogin:docgia'])->group(function () {
    
});

// API chung cho tất cả người dùng đã đăng nhập
Route::middleware(['isLogin', 'web'])->group(function () {
    // Route API lấy thông tin người dùng hiện tại
    Route::get('/user-info', function (Request $request) {
        return response()->json([
            'hoTen' => $request->session()->get('HoTen'),
            'email' => $request->session()->get('Email'),
            'quyen' => $request->session()->get('Quyen'),
        ]);
    });
    
});