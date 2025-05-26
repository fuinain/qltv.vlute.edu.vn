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

// Route cho trang thông tin sinh viên và lịch sử mượn - cần đăng nhập với quyền docgia
Route::middleware(['isLogin:docgia'])->group(function () {
    Route::get('/thong-tin-sinh-vien', function () {
        return view('app');
    });
    Route::get('/lich-su-muon', function () {
        return view('app');
    });
});

// Route dành cho sinh viên - chỉ sinh viên có thể truy cập
Route::prefix('docgia')->middleware(['isLogin:docgia'])->group(function () {
    Route::view("/{any?}", "app")->where("any", ".*");
});

// Route cho sinh viên không tồn tại
Route::get('/sinh-vien-khong-ton-tai', function () {
    return view('app');
});

// Route mặc định - giữ ở cuối
Route::view("/{any}", "app")->where("any", ".*");