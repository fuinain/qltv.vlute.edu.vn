<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DonViController;
use App\Http\Controllers\ChucVuController;
use App\Http\Controllers\HocKyController;
use App\Http\Controllers\ChuyenNganhController;

// API chỉ dành cho admin
Route::middleware(['isLogin:admin'])->group(function () {

    //API quản lý đơn vị
    Route::prefix('don-vi')->group(function () {
        Route::post('/', [DonViController::class, 'store']);         // C
        Route::get('/', [DonViController::class, 'index']);          // R
        Route::put('/{id}', [DonViController::class, 'update']);     // U
        Route::delete('/{id}', [DonViController::class, 'destroy']); // D
        Route::get('/list-don-vi-select-option', [DonViController::class, 'listDonViSelectOption']);          // R
    });

    //API quản lý chức vụ
    Route::prefix('chuc-vu')->group(function () {
        Route::post('/', [ChucVuController::class, 'store']);         // C
        Route::get('/', [ChucVuController::class, 'index']);          // R
        Route::put('/{id}', [ChucVuController::class, 'update']);     // U
        Route::delete('/{id}', [ChucVuController::class, 'destroy']); // D
    });

    //API quản lý học kỳ
    Route::prefix('hoc-ky')->group(function () {
        Route::get('/', [HocKyController::class, 'index']);          // R
        Route::post('/sync', [HocKyController::class, 'syncHocKy']);
    });

    //API quản lý chuyên ngành
    Route::prefix('chuyen-nganh')->group(function () {
        Route::post('/', [ChuyenNganhController::class, 'store']);         // C
        Route::get('/', [ChuyenNganhController::class, 'index']);          // R
        Route::put('/{id}', [ChuyenNganhController::class, 'update']);     // U
        Route::delete('/{id}', [ChuyenNganhController::class, 'destroy']); // D
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
