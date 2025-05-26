<?php

namespace App\Http\Controllers;

use App\Models\DocGiaModel;
use App\Models\SachModel;
use App\Models\TaiLieuModel;
use App\Models\BienMucBieuGhiModel;
use App\Models\BienMucTruongChaModel;
use App\Models\BienMucTruongConModel;
use App\Models\DKCBModel;
use App\Models\DocTaiChoModel;
use App\Models\MuonSachModel;
use App\Models\KhoAnPhamModel;
use App\Models\LichSuMuonTraModel;
use App\Models\DoiTuongBanDocModel;
use App\Models\ChiTietThamSoLuuThongModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OpacController extends Controller
{
    public function getThongKe()
    {
        $tongSoSach = SachModel::count();
        $tongSoBanDoc = DocGiaModel::count();
        
        return response()->json([
            'status' => 200,
            'data' => [
                'tongSoSach' => $tongSoSach,
                'tongSoBanDoc' => $tongSoBanDoc,
                // Lượt truy cập sẽ được cập nhật sau
            ]
        ]);
    }

    public function getSachMoi()
    {
        $sachMoi = SachModel::orderBy('ngay_tao', 'desc')
            ->limit(5)
            ->get(['id_sach', 'nhan_de', 'tac_gia', 'nha_xuat_ban', 'noi_xuat_ban', 'nam_xuat_ban']);
        
        return response()->json([
            'status' => 200,
            'data' => $sachMoi,
            'message' => 'Lấy danh sách sách mới thành công'
        ]);
    }
    
    public function getDanhSachSach(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search', '');
        $type = $request->input('type', 'all');
        
        $query = SachModel::query();
        
        if (!empty($search)) {
            if ($type === 'all') {
                // Tìm kiếm tất cả các trường
                $query->where(function($q) use ($search) {
                    $q->where('nhan_de', 'like', '%' . $search . '%')
                      ->orWhere('tac_gia', 'like', '%' . $search . '%')
                      ->orWhere('nha_xuat_ban', 'like', '%' . $search . '%')
                      ->orWhere('noi_xuat_ban', 'like', '%' . $search . '%')
                      ->orWhere('nam_xuat_ban', 'like', '%' . $search . '%');
                });
            } else {
                // Tìm kiếm theo trường cụ thể
                $query->where($type, 'like', '%' . $search . '%');
            }
        }
        
        $sach = $query->orderBy('ngay_tao', 'desc')
                      ->paginate($perPage);
        
        return response()->json([
            'status' => 200,
            'data' => $sach,
            'message' => 'Lấy danh sách sách thành công'
        ]);
    }
    
    public function getSachTheoTaiLieu($id_tai_lieu, Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search', '');
        
        // Lấy danh sách sách theo loại tài liệu
        $query = SachModel::join('bien_muc_bieu_ghi', 'sach.id_sach', '=', 'bien_muc_bieu_ghi.id_sach')
                          ->where('bien_muc_bieu_ghi.id_tai_lieu', $id_tai_lieu);
        
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('sach.nhan_de', 'like', '%' . $search . '%')
                  ->orWhere('sach.tac_gia', 'like', '%' . $search . '%')
                  ->orWhere('sach.nha_xuat_ban', 'like', '%' . $search . '%')
                  ->orWhere('sach.noi_xuat_ban', 'like', '%' . $search . '%')
                  ->orWhere('sach.nam_xuat_ban', 'like', '%' . $search . '%');
            });
        }
        
        $sach = $query->select('sach.*')
                      ->orderBy('sach.ngay_tao', 'desc')
                      ->paginate($perPage);
        
        // Lấy tên tài liệu
        $taiLieu = TaiLieuModel::find($id_tai_lieu);
        $tenTaiLieu = $taiLieu ? $taiLieu->ten_tai_lieu : 'Không xác định';
        
        return response()->json([
            'status' => 200,
            'data' => $sach,
            'tenTaiLieu' => $tenTaiLieu,
            'message' => 'Lấy danh sách sách theo loại tài liệu thành công'
        ]);
    }
    
    public function getChiTietSach($id_sach)
    {
        // Lấy thông tin sách
        $sach = SachModel::find($id_sach);
        
        if (!$sach) {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy sách'
            ], 404);
        }
        
        // Lấy biểu ghi của sách
        $bieuGhi = BienMucBieuGhiModel::where('id_sach', $id_sach)->first();
        
        if (!$bieuGhi) {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy biểu ghi của sách'
            ], 404);
        }
        
        // Lấy thông tin tài liệu
        $taiLieu = null;
        if ($bieuGhi->id_tai_lieu) {
            $taiLieu = TaiLieuModel::find($bieuGhi->id_tai_lieu);
        }
        
        // Lấy các trường MARC
        $truongCha = BienMucTruongChaModel::where('id_bien_muc_bieu_ghi', $bieuGhi->id_bien_muc_bieu_ghi)->get();
        
        // Lấy thông tin mô tả
        $moTa = [
            'nhan_de' => $this->getNoiDungTruong($truongCha, '245', 'a'),
            'tac_gia' => $this->getNoiDungTruong($truongCha, '100', 'a'),
            'xuat_ban' => $this->getNoiDungTruongNhieu($truongCha, '260', ['a', 'b', 'c']),
            'mo_ta_vat_ly' => $this->getNoiDungTruongNhieu($truongCha, '300', ['a', 'c']),
            'tac_gia_khac' => $this->getNoiDungTruong($truongCha, '245', 'c'),
            'tu_khoa' => $this->getNoiDungTruong($truongCha, '630', 'a'),
            'so_phan_loai' => $this->getNoiDungTruongNhieu($truongCha, '082', ['a', 'b']),
        ];
        
        // Lấy danh sách DKCB
        $dkcbs = DKCBModel::where('id_sach', $id_sach)->get();
        
        $danhSachDKCB = [];
        foreach ($dkcbs as $dkcb) {
            // Kiểm tra tình trạng mượn
            $dangMuon = DocTaiChoModel::where('id_dkcb', $dkcb->id_dkcb)->exists() ||
                        MuonSachModel::where('id_dkcb', $dkcb->id_dkcb)->exists();
            
            // Lấy thông tin kho
            $kho = KhoAnPhamModel::find($dkcb->id_kho_an_pham);
            
            $danhSachDKCB[] = [
                'ma_dkcb' => $dkcb->ma_dkcb,
                'tinh_trang' => $dangMuon ? 'Đang được mượn' : 'Sẵn sàng',
                'kho_luu_tru' => $kho ? $kho->ten_kho : 'Không xác định'
            ];
        }
        
        // Lấy tất cả trường MARC
        $marcData = [];
        foreach ($truongCha as $cha) {
            $truongCon = BienMucTruongConModel::where('id_bien_muc_truong_cha', $cha->id_bien_muc_truong_cha)
                                             ->orderByRaw("CASE WHEN ma_truong_con REGEXP '^[0-9]+$' THEN 0 ELSE 1 END")
                                             ->orderByRaw("CAST(ma_truong_con AS UNSIGNED), ma_truong_con")
                                             ->get();
            
            $marcData[] = [
                'ma_truong' => $cha->ma_truong,
                'ct1' => $cha->ct1,
                'ct2' => $cha->ct2,
                'children' => $truongCon
            ];
        }
        
        // Trả về dữ liệu
        return response()->json([
            'status' => 200,
            'data' => [
                'sach' => $sach,
                'bieuGhi' => $bieuGhi,
                'taiLieu' => $taiLieu ? $taiLieu->ten_tai_lieu : null,
                'moTa' => $moTa,
                'danhSachDKCB' => $danhSachDKCB,
                'marcData' => $marcData
            ],
            'message' => 'Lấy chi tiết sách thành công'
        ]);
    }
    
    // Hàm hỗ trợ lấy nội dung trường
    private function getNoiDungTruong($truongCha, $maTruong, $maTruongCon)
    {
        $truong = $truongCha->where('ma_truong', $maTruong)->first();
        
        if (!$truong) {
            return null;
        }
        
        $truongCon = BienMucTruongConModel::where('id_bien_muc_truong_cha', $truong->id_bien_muc_truong_cha)
                                         ->where('ma_truong_con', $maTruongCon)
                                         ->first();
        
        return $truongCon ? $truongCon->noi_dung : null;
    }
    
    // Hàm hỗ trợ lấy nhiều nội dung trường
    private function getNoiDungTruongNhieu($truongCha, $maTruong, $maTruongCons)
    {
        $truong = $truongCha->where('ma_truong', $maTruong)->first();
        
        if (!$truong) {
            return null;
        }
        
        $result = [];
        
        foreach ($maTruongCons as $maTruongCon) {
            $truongCon = BienMucTruongConModel::where('id_bien_muc_truong_cha', $truong->id_bien_muc_truong_cha)
                                             ->where('ma_truong_con', $maTruongCon)
                                             ->first();
            
            if ($truongCon && !empty($truongCon->noi_dung)) {
                $result[] = $truongCon->noi_dung;
            }
        }
        
        return !empty($result) ? implode(' - ', $result) : null;
    }
    
    // Phương thức lấy thông tin sinh viên
    public function getThongTinSinhVien($id_doc_gia)
    {
        try {
            // Kiểm tra xem id_doc_gia có khớp với session không
            if (session('IdDocGia') != $id_doc_gia) {
                return response()->json([
                    'status' => 403,
                    'message' => 'Bạn không có quyền truy cập thông tin này'
                ]);
            }

            // Lấy thông tin sinh viên từ bảng doc_gia và kết hợp với bảng chuyen_nganh
            $docGia = DB::table('doc_gia')
                ->leftJoin('chuyen_nganh', 'doc_gia.id_chuyen_nganh', '=', 'chuyen_nganh.id_chuyen_nganh')
                ->select(
                    'doc_gia.*',
                    'chuyen_nganh.ten_chuyen_nganh'
                )
                ->where('doc_gia.id_doc_gia', $id_doc_gia)
                ->first();

            if (!$docGia) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Không tìm thấy thông tin sinh viên'
                ]);
            }

            return response()->json([
                'status' => 200,
                'data' => $docGia
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage()
            ]);
        }
    }

    // Phương thức lấy lịch sử mượn sách của sinh viên
    public function getLichSuMuon($id_doc_gia)
    {
        try {
            // Kiểm tra xem id_doc_gia có khớp với session không
            if (session('IdDocGia') != $id_doc_gia) {
                return response()->json([
                    'status' => 403,
                    'message' => 'Bạn không có quyền truy cập thông tin này'
                ]);
            }

            $lichSuMuon = [];

            // 1. Lấy sách đang mượn từ bảng muon_sach
            $sachDangMuon = DB::table('muon_sach')
                ->join('dkcb', 'muon_sach.id_dkcb', '=', 'dkcb.id_dkcb')
                ->join('sach', 'dkcb.id_sach', '=', 'sach.id_sach')
                ->select(
                    'muon_sach.id_muon_sach',
                    'muon_sach.ngay_muon',
                    'muon_sach.han_tra',
                    'muon_sach.gia_han',
                    DB::raw('NULL as ngay_tra'),
                    'dkcb.ma_dkcb',
                    'sach.id_sach',
                    'sach.nhan_de as ten_sach',
                    'sach.tac_gia',
                    DB::raw('CASE 
                        WHEN muon_sach.qua_han = 1 THEN "Quá hạn"
                        ELSE "Đang mượn"
                    END as trang_thai')
                )
                ->where('muon_sach.id_ban_doc', $id_doc_gia)
                ->get();
            
            // Thêm sách đang mượn vào kết quả
            foreach ($sachDangMuon as $sach) {
                $lichSuMuon[] = $sach;
            }

            // 2. Lấy sách đọc tại chỗ từ bảng doc_tai_cho
            $sachDocTaiCho = DB::table('doc_tai_cho')
                ->join('dkcb', 'doc_tai_cho.id_dkcb', '=', 'dkcb.id_dkcb')
                ->join('sach', 'dkcb.id_sach', '=', 'sach.id_sach')
                ->select(
                    'doc_tai_cho.id_doc_tai_cho as id_muon_sach',
                    'doc_tai_cho.gio_muon as ngay_muon',
                    DB::raw('NULL as han_tra'),
                    DB::raw('0 as gia_han'),
                    'doc_tai_cho.gio_tra as ngay_tra',
                    'dkcb.ma_dkcb',
                    'sach.id_sach',
                    'sach.nhan_de as ten_sach',
                    'sach.tac_gia',
                    DB::raw('CASE 
                        WHEN doc_tai_cho.qua_han = 1 THEN "Quá hạn"
                        WHEN doc_tai_cho.gio_tra IS NOT NULL THEN "Đã trả"
                        ELSE "Đọc tại chỗ"
                    END as trang_thai')
                )
                ->where('doc_tai_cho.id_ban_doc', $id_doc_gia)
                ->get();
            
            // Thêm sách đọc tại chỗ vào kết quả
            foreach ($sachDocTaiCho as $sach) {
                $lichSuMuon[] = $sach;
            }

            // 3. Lấy lịch sử mượn trả từ bảng lich_su_muon_tra
            $lichSuMuonTra = DB::table('lich_su_muon_tra')
                ->select(
                    'lich_su_muon_tra.id_lich_su as id_muon_sach',
                    'lich_su_muon_tra.ngay_muon',
                    'lich_su_muon_tra.han_tra',
                    'lich_su_muon_tra.gia_han',
                    'lich_su_muon_tra.ngay_tra',
                    'lich_su_muon_tra.ma_dkcb',
                    DB::raw('NULL as id_sach'),
                    'lich_su_muon_tra.ten_sach',
                    DB::raw('NULL as tac_gia'),
                    DB::raw('"Đã trả" as trang_thai')
                )
                ->where('lich_su_muon_tra.id_ban_doc', $id_doc_gia)
                ->get();
            
            // Thêm lịch sử mượn trả vào kết quả
            foreach ($lichSuMuonTra as $sach) {
                $lichSuMuon[] = $sach;
            }

            // Sắp xếp kết quả theo ngày mượn giảm dần (mới nhất lên đầu)
            $lichSuMuon = collect($lichSuMuon)->sortByDesc('ngay_muon')->values();

            return response()->json([
                'status' => 200,
                'data' => $lichSuMuon
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage()
            ]);
        }
    }

    // Phương thức gia hạn sách cho sinh viên
    public function giaHanSach($id_muon_sach)
    {
        try {
            // Tìm phiếu mượn
            $phieuMuon = MuonSachModel::find($id_muon_sach);
            
            if (!$phieuMuon) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Không tìm thấy phiếu mượn'
                ]);
            }

            // Kiểm tra quyền truy cập (chỉ sinh viên sở hữu phiếu mới được gia hạn)
            if (session('IdDocGia') != $phieuMuon->id_ban_doc) {
                return response()->json([
                    'status' => 403,
                    'message' => 'Bạn không có quyền gia hạn phiếu mượn này'
                ]);
            }

            // Kiểm tra đã gia hạn chưa
            if ($phieuMuon->gia_han > 0) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Phiếu mượn đã được gia hạn trước đó'
                ]);
            }

            // Kiểm tra điều kiện gia hạn (phải đến ngày hạn trả mới được gia hạn)
            $today = Carbon::now()->startOfDay();
            $hanTra = Carbon::parse($phieuMuon->han_tra)->startOfDay();
            
            if ($today->lt($hanTra)) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Chưa đến hạn trả, không thể gia hạn. Vui lòng gia hạn vào ngày ' . $hanTra->format('d/m/Y')
                ]);
            }

            // Lấy thông tin bạn đọc, đối tượng bạn đọc và kho
            $banDoc = DocGiaModel::find($phieuMuon->id_ban_doc);
            $dkcb = DKCBModel::find($phieuMuon->id_dkcb);
            
            if (!$banDoc || !$dkcb) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Không tìm thấy thông tin bạn đọc hoặc DKCB'
                ]);
            }

            $doiTuongBanDoc = DoiTuongBanDocModel::where('ma_so_quy_uoc', $banDoc->ma_so_quy_uoc)->first();
            
            if (!$doiTuongBanDoc) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Không tìm thấy thông tin đối tượng bạn đọc'
                ]);
            }

            // Lấy thông tin gia hạn từ cấu hình
            $diemLuuThongID = 1; // Giả sử id=1 là kho mượn về
            $thongTinMuon = ChiTietThamSoLuuThongModel::join('kho_an_pham', 'chi_tiet_tham_so_lt.id_kho_an_pham', '=', 'kho_an_pham.id_kho_an_pham')
                ->where('chi_tiet_tham_so_lt.id_doi_tuong_ban_doc', $doiTuongBanDoc->id_doi_tuong_ban_doc)
                ->where('chi_tiet_tham_so_lt.id_diem_luu_thong', $diemLuuThongID)
                ->where('chi_tiet_tham_so_lt.id_kho_an_pham', $dkcb->id_kho_an_pham)
                ->select('chi_tiet_tham_so_lt.*')
                ->first();

            if (!$thongTinMuon || $thongTinMuon->gia_han <= 0) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Đối tượng bạn đọc không được phép gia hạn hoặc số ngày gia hạn là 0'
                ]);
            }

            // Tính ngày gia hạn
            $soNgayGiaHan = (int)$thongTinMuon->gia_han;
            $hanTraMoi = Carbon::parse($phieuMuon->han_tra)->addDays($soNgayGiaHan);

            // Cập nhật phiếu mượn
            $phieuMuon->han_tra = $hanTraMoi;
            $phieuMuon->gia_han = 1; // Đánh dấu đã gia hạn
            $phieuMuon->save();

            // Cập nhật lịch sử mượn trả
            $lichSu = LichSuMuonTraModel::where('id_ban_doc', $phieuMuon->id_ban_doc)
                ->where('id_dkcb', $phieuMuon->id_dkcb)
                ->whereNull('ngay_tra')
                ->where('tai_cho', 0) // 0 = mượn về
                ->first();
                
            if ($lichSu) {
                $lichSu->han_tra = $hanTraMoi;
                $lichSu->gia_han = 1;
                $lichSu->save();
            }

            return response()->json([
                'status' => 200,
                'message' => 'Gia hạn thành công',
                'data' => [
                    'id_muon_sach' => $phieuMuon->id_muon_sach,
                    'han_tra' => $phieuMuon->han_tra,
                    'gia_han' => $phieuMuon->gia_han
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage()
            ]);
        }
    }
}
