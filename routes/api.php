<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DonViController;
use App\Http\Controllers\ChucVuController;
use App\Http\Controllers\HocKyController;

// API chỉ dành cho admin
Route::middleware(['isLogin:admin'])->group(function () {
    
    //API quản lý đơn vị
    Route::prefix('donvi')->group(function () {
        Route::post('/', [DonViController::class, 'store']);         // C
        Route::get('/', [DonViController::class, 'index']);          // R
        Route::put('/{id}', [DonViController::class, 'update']);     // U
        Route::delete('/{id}', [DonViController::class, 'destroy']); // D
    });

    //API quản lý chức vụ
    Route::prefix('chucvu')->group(function () {
        Route::post('/', [ChucVuController::class, 'store']);         // C
        Route::get('/', [ChucVuController::class, 'index']);          // R
        Route::put('/{id}', [ChucVuController::class, 'update']);     // U
        Route::delete('/{id}', [ChucVuController::class, 'destroy']); // D
    });

    //API quản lý học kỳ
    Route::prefix('hocky')->group(function () {
        Route::get('/', [HocKyController::class, 'index']);          // R
        Route::post('/sync', [HocKyController::class, 'syncHocKy']);
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