<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SSOController;
use Illuminate\Http\Request;

// Trang chủ OPAC


// Các route xác thực
Route::get('/login', [SSOController::class, 'dangNhap'])->name('login');
Route::get('/login/callback', [SSOController::class, 'callback'])->name('callback');
Route::get('/logout', [SSOController::class, 'logout'])->name('logout');
Route::get('/thayDoiMatKhau', [SSOController::class, 'thayDoiMatKhau'])->name('thayDoiMatKhau');

// Route dành cho admin - chỉ admin có thể truy cập
Route::prefix('admin')->middleware(['isLogin:admin'])->group(function () {
    Route::view("/{any?}", "app")->where("any", ".*");
});

// Route dành cho sinh viên - chỉ sinh viên có thể truy cập
Route::prefix('docgia')->middleware(['isLogin:docgia'])->group(function () {
    Route::view("/{any?}", "app")->where("any", ".*");
});

// Route mặc định - giữ ở cuối
Route::view("/{any}", "app")->where("any", ".*");