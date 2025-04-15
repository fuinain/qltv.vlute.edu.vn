<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DonViController;
use App\Http\Controllers\ChucVuController;
use App\Http\Controllers\HocKyController;
use App\Http\Controllers\ChuyenNganhController;
use App\Http\Controllers\LopHocController;
use App\Http\Controllers\DoiTuongBanDocController;

// API chỉ dành cho admin
Route::middleware(['isLogin:admin'])->group(function () {

    //API Danh mục
    Route::group(['prefix' => 'danh-muc'], function () {

        //API Thông tin chung
        Route::group(['prefix' => 'thong-tin-chung'], function () {

            //API quản lý đơn vị
            Route::prefix('don-vi')->group(function () {
                Route::post('/', [DonViController::class, 'store']);
                Route::get('/', [DonViController::class, 'index']);
                Route::put('/{id}', [DonViController::class, 'update']);
                Route::delete('/{id}', [DonViController::class, 'destroy']);
                Route::get('/list-don-vi-select-option', [DonViController::class, 'listDonViSelectOption']);       
            });

            //API quản lý chức vụ
            Route::prefix('chuc-vu')->group(function () {
                Route::post('/', [ChucVuController::class, 'store']);
                Route::get('/', [ChucVuController::class, 'index']);
                Route::put('/{id}', [ChucVuController::class, 'update']);
                Route::delete('/{id}', [ChucVuController::class, 'destroy']);
            });

            //API quản lý học kỳ
            Route::prefix('hoc-ky')->group(function () {
                Route::get('/', [HocKyController::class, 'index']);
                Route::post('/sync', [HocKyController::class, 'syncHocKy']);
                Route::get('/list-khoa-hoc', [HocKyController::class, 'listKhoaHoc']);
                Route::get('/list-nam-hoc', [HocKyController::class, 'listNamHoc']);
                Route::get('/list-nien-khoa', [HocKyController::class, 'listNienKhoa']);
            });

            Route::prefix('lop-hoc')->group(function () {
                Route::post('/', [LopHocController::class, 'store']);
                Route::get('/', [LopHocController::class, 'index']);
                Route::get('/{id}', [LopHocController::class, 'show']);
                Route::put('/{id}', [LopHocController::class, 'update']);
                Route::delete('/{id}', [LopHocController::class, 'destroy']);
            });

            //API quản lý chuyên ngành
            Route::prefix('chuyen-nganh')->group(function () {
                Route::post('/', [ChuyenNganhController::class, 'store']);
                Route::get('/', [ChuyenNganhController::class, 'index']);
                Route::put('/{id}', [ChuyenNganhController::class, 'update']);
                Route::delete('/{id}', [ChuyenNganhController::class, 'destroy']);
            });
        });

        Route::group(['prefix' => 'nghiep-vu-luu-thong'], function () {

            //API đối tượng bạn đọc
            Route::prefix('doi-tuong-ban-doc')->group(function () {
                Route::post('/', [DoiTuongBanDocController::class, 'store']);
                Route::get('/', [DoiTuongBanDocController::class, 'index']);
                Route::put('/{id}', [DoiTuongBanDocController::class, 'update']);
                Route::delete('/{id}', [DoiTuongBanDocController::class, 'destroy']);
                Route::get('/list-dtbd-select-option', [DoiTuongBanDocController::class, 'listDoiTuongBanDocSelectOption']);
            });
        });
    });
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
