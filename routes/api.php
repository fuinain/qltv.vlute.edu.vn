<?php

use App\Http\Controllers\BienMucBieuGhiController;
use App\Http\Controllers\MarcController;
use App\Http\Controllers\ThamSoLuuThongController;
use App\Http\Controllers\TrangThaiDonController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DonViController;
use App\Http\Controllers\ChucVuController;
use App\Http\Controllers\HocKyController;
use App\Http\Controllers\ChuyenNganhController;
use App\Http\Controllers\LopHocController;
use App\Http\Controllers\DoiTuongBanDocController;
use App\Http\Controllers\TaiLieuController;
use App\Http\Controllers\KhoAnPhamController;
use App\Http\Controllers\DiemLuuThongController;
use App\Http\Controllers\PhatBanDocController;
use App\Http\Controllers\NhaCungCapController;
use App\Http\Controllers\NguonNhanController;
use App\Http\Controllers\LoaiNhapController;
use App\Http\Controllers\DonNhanController;
use App\Http\Controllers\SachController;
use App\Http\Controllers\DKCBController;
use App\Http\Controllers\DocGiaController;
use App\Http\Controllers\MuonSachController;
use App\Http\Controllers\DocTaiChoController;
use App\Http\Controllers\DMBaoCaoController;
use App\Http\Controllers\XuLyViPhamController;
use App\Http\Controllers\BaiVietController;
use App\Http\Controllers\OpacController;

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
                Route::get('/list-chuc-vu-select-option', [ChucVuController::class, 'listChucVuSelectOption']);
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
                Route::get('/by-don-vi/{id_don_vi}', [ChuyenNganhController::class, 'listByDonViSelectOption']);
                Route::get('/list-chuyen-nganh-select-option', [ChuyenNganhController::class, 'listChuyenNganhSelectOption']);
            });
        });

        Route::group(['prefix' => 'nghiep-vu-bien-muc'], function () {

            //API nghiệp vụ biên mục
            Route::prefix('tai-lieu')->group(function () {
                Route::post('/', [TaiLieuController::class, 'store']);
                Route::get('/', [TaiLieuController::class, 'index']);
                Route::put('/{id}', [TaiLieuController::class, 'update']);
                Route::delete('/{id}', [TaiLieuController::class, 'destroy']);
                Route::get('/list-tai-lieu-select-option', [TaiLieuController::class, 'listTaiLieuSelectOption']);

            });
        });

        Route::group(['prefix' => 'nghiep-vu-bo-sung'], function () {

            //API NCC
            Route::prefix('ncc')->group(function () {
                Route::post('/', [NhaCungCapController::class, 'store']);
                Route::get('/', [NhaCungCapController::class, 'index']);
                Route::put('/{id}', [NhaCungCapController::class, 'update']);
                Route::delete('/{id}', [NhaCungCapController::class, 'destroy']);
                Route::get('/list-ncc-select-option', [NhaCungCapController::class, 'listNCCSelectOption']);

            });

            //API Trạng thái đơn
            Route::prefix('trang-thai-don')->group(function () {
                Route::post('/', [TrangThaiDonController::class, 'store']);
                Route::get('/', [TrangThaiDonController::class, 'index']);
                Route::put('/{id}', [TrangThaiDonController::class, 'update']);
                Route::delete('/{id}', [TrangThaiDonController::class, 'destroy']);
                Route::get('/list-trang-thai-don-select-option', [TrangThaiDonController::class, 'listTTDonSelectOption']);
            });

            //API Nguồn nhận
            Route::prefix('nguon-nhan')->group(function () {
                Route::post('/', [NguonNhanController::class, 'store']);
                Route::get('/', [NguonNhanController::class, 'index']);
                Route::put('/{id}', [NguonNhanController::class, 'update']);
                Route::delete('/{id}', [NguonNhanController::class, 'destroy']);
                Route::get('/list-nguon-nhan-select-option', [NguonNhanController::class, 'listNguonNhanSelectOption']);
            });

            //API Loại nhập
            Route::prefix('loai-nhap')->group(function () {
                Route::post('/', [LoaiNhapController::class, 'store']);
                Route::get('/', [LoaiNhapController::class, 'index']);
                Route::put('/{id}', [LoaiNhapController::class, 'update']);
                Route::delete('/{id}', [LoaiNhapController::class, 'destroy']);
                Route::get('/list-loai-nhap-select-option', [LoaiNhapController::class, 'listLoaiNhapSelectOption']);
            });

            //API Báo cáo
            Route::prefix('dm-bao-cao')->group(function () {
                Route::post('/', [DMBaoCaoController::class, 'store']);
                Route::get('/', [DMBaoCaoController::class, 'index']);
                Route::put('/{id}', [DMBaoCaoController::class, 'update']);
                Route::delete('/{id}', [DMBaoCaoController::class, 'destroy']);
                Route::get('/list-dm-bao-cao-select-option', [DMBaoCaoController::class, 'listDMBaoCaoSelectOption']);
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
                Route::get('/thong-ke', [DoiTuongBanDocController::class, 'thongKeDoiTuongBanDoc']);
                Route::get('/chi-tiet/{ma_so_quy_uoc}', [DoiTuongBanDocController::class, 'chiTietDocGiaTheoDoiTuong']);
            });

            //API điểm lưu thông
            Route::prefix('diem-luu-thong')->group(function () {
                Route::post('/', [DiemLuuThongController::class, 'store']);
                Route::get('/', [DiemLuuThongController::class, 'index']);
                Route::put('/{id}', [DiemLuuThongController::class, 'update']);
                Route::delete('/{id}', [DiemLuuThongController::class, 'destroy']);

                Route::prefix('chi-tiet-luu-thong')->group(function () {
                    Route::get('/{id}', [ThamSoLuuThongController::class, 'index']);
                    Route::post('/chi-tiet-tslt', [ThamSoLuuThongController::class, 'taoHoacCapNhat']);
                    Route::get('/get-chi-tiet-tslt/{id_doi_tuong}/{id_diem}', [ThamSoLuuThongController::class, 'layChiTiet']);
                    Route::put('/update-chi-tiet-tslt', [ThamSoLuuThongController::class, 'capNhatChiTiet']);
                });
            });

            //API phạt bạn đọc
            Route::prefix('phat-ban-doc')->group(function () {
                Route::post('/', [PhatBanDocController::class, 'store']);
                Route::get('/', [PhatBanDocController::class, 'index']);
                Route::put('/{id}', [PhatBanDocController::class, 'update']);
                Route::delete('/{id}', [PhatBanDocController::class, 'destroy']);
                Route::get('/list-phat-ban-doc-select-option', [PhatBanDocController::class, 'listPhatBanDocSelectOption']);
            });
        });
    });

    //API Quản lý ẩn phẩm
    Route::group(['prefix' => 'quan-ly-an-pham'], function () {
        //API Quản lý nhận sách
        Route::prefix('nhan-sach')->group(function () {

            // API Quản lý đơn nhận
            Route::prefix('don-nhan')->group(function () {
                Route::post('/', [DonNhanController::class, 'store']);
                Route::get('/', [DonNhanController::class, 'index']);
                Route::put('/{id}', [DonNhanController::class, 'update']);
                Route::delete('/{id}', [DonNhanController::class, 'destroy']);

                //API Chi tiết đơn nhận
                Route::prefix('chi-tiet-don-nhan')->group(function () {
                    Route::post('/', [SachController::class, 'store']);
                    Route::get('/{id_don_nhan}', [SachController::class, 'index']);
                    Route::put('/{id}', [SachController::class, 'update']);
                    Route::delete('/{id}', [SachController::class, 'destroy']);
                    Route::get('/export-don-nhan/{id_don_nhan}', [SachController::class, 'exportExcelDonNhan']);
                    Route::get('/export-thong-ke-tai-lieu/{id_don_nhan}', [SachController::class, 'exportExcelThongKeTaiLieu']);

                    // Routes cho chức năng gán DKCB
                    Route::get('/sach/{id}', [SachController::class, 'show']);
                    Route::get('/tim-dkcb', [SachController::class, 'timDKCB']);
                    Route::post('/gan-dkcb', [SachController::class, 'ganDKCB']);
                    Route::get('/sach/dkcb/{id_sach}', [SachController::class, 'getDKCBBySach']);

                    //API Biên mục biểu ghi
                    Route::prefix('bien-muc-bieu-ghi')->group(function () {
                        Route::get('/{id_sach}', [BienMucBieuGhiController::class, 'show']);
                        Route::post('/', [BienMucBieuGhiController::class, 'store']);

                        Route::prefix('bien-muc/marc')->group(function () {
                            // Lấy toàn bộ cha–con của một cuốn
                            Route::get('/by-sach/{id_sach}', [MarcController::class, 'index']);

                            /* ---------- TRƯỜNG CHA ---------- */
                            Route::post('/parent', [MarcController::class, 'storeParent']);   // thêm mới
                            Route::put('/parent/{id}', [MarcController::class, 'updateParent']);  // cập nhật
                            Route::delete('/parent/{id}', [MarcController::class, 'destroyParent']); // xoá

                            // thêm cha rỗng ngay sau vị trí idx (FE truyền idx)
                            Route::post('/add-parent-after', [MarcController::class, 'addParentAfter']);

                            /* ---------- TRƯỜNG CON ---------- */
                            Route::post('/child', [MarcController::class, 'storeChild']);     // thêm mới
                            Route::put('/child/{id}', [MarcController::class, 'updateChild']);    // cập nhật
                            Route::delete('/child/{id}', [MarcController::class, 'destroyChild']);   // xoá
                        });
                    });
                });
            });

            //API Báo cáo nhận sách phân kho
            Route::prefix('bao-cao-nhan-sach-phan-kho')->group(function () {
                Route::get('/export', [\App\Http\Controllers\BCNhanSachPhanKhoController::class, 'export']);
            });
        });

        //API In nhãn phân loại
        Route::prefix('in-nhan')->group(function () {
            //API Nhãn DKCB
            Route::prefix('dkcb')->group(function () {
                Route::get('/', [DKCBController::class, 'soNhan']);
                Route::post('/tao-nhan', [DKCBController::class, 'taoNhanDKCB']);
                Route::post('/in-nhan', [DKCBController::class, 'inNhanDKCB']);
                Route::get('/danh-sach-nhan/{id_kho}', [DKCBController::class, 'danhSachNhanTheoKho']);
            });

            //API In nhãn phân loại
            Route::prefix('phan-loai')->group(function () {
                Route::post('/danh-sach-sach', [SachController::class, 'danhSachSachTheoDonNhan']);
                Route::post('/tao-nhan', [SachController::class, 'taoNhanPhanLoai']);
            });
        });

        //API Kho ấn phẩm
        Route::prefix('kho-an-pham')->group(function () {

            // API Danh mục kho
            Route::prefix('danh-muc-kho')->group(function () {
                Route::post('/', [KhoAnPhamController::class, 'store']);
                Route::get('/', [KhoAnPhamController::class, 'index']);
                Route::put('/{id}', [KhoAnPhamController::class, 'update']);
                Route::delete('/{id}', [KhoAnPhamController::class, 'destroy']);
                Route::get('/list-dmkho-select-option', [KhoAnPhamController::class, 'listDanhMucKhoAnPhamSelectOption']);
            });
        });
    });

    // Quản lý bạn đọc
    Route::group(['prefix' => 'quan-ly-ban-doc'], function () {
        Route::prefix('doc-gia')->group(function () {
            Route::get('/list-dtbd-for-sync', [DocGiaController::class, 'listDoiTuongBanDocForSync']);
            Route::get('/list-chuyen-nganh-for-sync', [DocGiaController::class, 'listChuyenNganhForSync']);
            Route::get('/', [DocGiaController::class, 'index']);
            Route::post('/', [DocGiaController::class, 'store']);
            Route::get('/{id}', [DocGiaController::class, 'show']);
            Route::put('/{id}', [DocGiaController::class, 'update']);
            Route::delete('/{id}', [DocGiaController::class, 'destroy']);
            Route::post('/sync', [DocGiaController::class, 'syncBanDoc']);
        });
    });

    // Quản lý dịch vụ
    Route::group(['prefix' => 'quan-ly-dich-vu'], function () {
        Route::prefix('muon-tra')->group(function () {
            Route::get('/ban-doc/{search_value}', [MuonSachController::class, 'timBanDoc']);
            Route::get('/kiem-tra-dkcb/{ma_dkcb}', [MuonSachController::class, 'kiemTraDKCB']);
            Route::post('/muon', [MuonSachController::class, 'muon']);
            Route::put('/gia-han/{id_muon_sach}', [MuonSachController::class, 'giaHan']);
            Route::put('/tra-sach/{id_muon_sach}', [MuonSachController::class, 'traSach']);
        });

        Route::prefix('doc-tai-cho')->group(function () {
            Route::get('/ban-doc/{search_value}', [DocTaiChoController::class, 'timBanDoc']);
            Route::get('/kiem-tra-dkcb/{ma_dkcb}', [DocTaiChoController::class, 'kiemTraDKCB']);
            Route::post('/muon', [DocTaiChoController::class, 'muon']);
            Route::put('/tra-sach/{id_doc_tai_cho}', [DocTaiChoController::class, 'traSach']);
        });

        Route::prefix('xu-ly-vi-pham')->group(function () {
            Route::get('/ban-doc/{mssv}', [XuLyViPhamController::class, 'timBanDoc']);
            Route::post('/them-vi-pham', [XuLyViPhamController::class, 'themViPham']);
            Route::get('/danh-sach-vi-pham/{mssv}', [XuLyViPhamController::class, 'danhSachViPham']);
            Route::delete('/xoa-vi-pham/{id}', [XuLyViPhamController::class, 'xoaViPham']);
        });

        Route::prefix('bao-cao')->group(function () {
            Route::post('/thong-ke-sach-dang-muon', [DMBaoCaoController::class, 'thongKeSachDangMuon']);
            Route::post('/xuat-excel-sach-dang-muon', [DMBaoCaoController::class, 'xuatExcelSachDangMuon']);
            Route::post('/thong-ke-sach-da-tra', [DMBaoCaoController::class, 'thongKeSachDaTra']);
            Route::post('/xuat-excel-sach-da-tra', [DMBaoCaoController::class, 'xuatExcelSachDaTra']);
            Route::post('/thong-ke-sach-qua-han', [DMBaoCaoController::class, 'thongKeSachQuaHan']);
            Route::post('/xuat-excel-sach-qua-han', [DMBaoCaoController::class, 'xuatExcelSachQuaHan']);
            Route::post('/thong-ke-ban-doc-den-thu-vien', [DMBaoCaoController::class, 'thongKeBanDocDenThuVien']);
            Route::post('/xuat-excel-ban-doc-den-thu-vien', [DMBaoCaoController::class, 'xuatExcelBanDocDenThuVien']);
            Route::post('/thong-ke-tinh-hinh-phuc-vu-ban-doc', [DMBaoCaoController::class, 'thongKeTinhHinhPhucVuBanDoc']);
            Route::post('/xuat-excel-tinh-hinh-phuc-vu-ban-doc', [DMBaoCaoController::class, 'xuatExcelTinhHinhPhucVuBanDoc']);
        });

        Route::prefix('bai-viet')->group(function () {
            Route::get('/', [BaiVietController::class, 'index']);
            Route::post('/', [BaiVietController::class, 'store']);
            Route::put('/{id}', [BaiVietController::class, 'update']);
            Route::delete('/{id}', [BaiVietController::class, 'destroy']);
            Route::get('/chu-de', [BaiVietController::class, 'getChuDeBaiViet']);
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

// API công khai không cần đăng nhập
Route::prefix('opac')->group(function () {
    Route::get('/bai-viet/{type}', [BaiVietController::class, 'getBaiVietByNavbar']);
    Route::get('/danh-sach-tai-lieu', [TaiLieuController::class, 'listTaiLieuSelectOption']);
    Route::get('/thong-ke', [OpacController::class, 'getThongKe']);
    Route::get('/danh-sach-bai-viet-theo-chu-de/{ten_chu_de}', [BaiVietController::class, 'getBaiVietByTenChuDe']);
    Route::get('/chi-tiet-bai-viet/{id}', [BaiVietController::class, 'getChiTietBaiViet']);
    Route::get('/sach-moi', [OpacController::class, 'getSachMoi']);
    Route::get('/danh-sach-sach', [OpacController::class, 'getDanhSachSach']);
    Route::get('/sach-theo-tai-lieu/{id_tai_lieu}', [OpacController::class, 'getSachTheoTaiLieu']);
    Route::get('/chi-tiet-sach/{id_sach}', [OpacController::class, 'getChiTietSach']);
});


