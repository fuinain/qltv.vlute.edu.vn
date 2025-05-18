<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MuonSachModel;
use App\Models\DocGiaModel;
use App\Models\DKCBModel;
use App\Models\SachModel;
use App\Models\KhoAnPhamModel;
use App\Models\DoiTuongBanDocModel;
use App\Models\ChiTietThamSoLuuThongModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MuonSachController extends Controller
{
    // Tìm kiếm bạn đọc
    public function timBanDoc($searchValue)
    {
        // Tìm bạn đọc theo MSSV hoặc số thẻ
        $banDoc = DocGiaModel::where('mssv', $searchValue)
            ->orWhere('so_the', $searchValue)
            ->first();

        if (!$banDoc) {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy thông tin bạn đọc'
            ]);
        }

        // Lấy thông tin đối tượng bạn đọc
        $doiTuongBanDoc = DoiTuongBanDocModel::where('ma_so_quy_uoc', $banDoc->ma_so_quy_uoc)->first();
        
        if (!$doiTuongBanDoc) {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy thông tin đối tượng bạn đọc'
            ]);
        }

        // Lấy danh sách tài liệu đang mượn
        $danhSachMuon = DB::table('muon_sach as ms')
            ->join('dkcb', 'ms.id_dkcb', '=', 'dkcb.id_dkcb')
            ->join('sach', 'dkcb.id_sach', '=', 'sach.id_sach')
            ->join('kho_an_pham as kap', 'dkcb.id_kho_an_pham', '=', 'kap.id_kho_an_pham')
            ->where('ms.id_ban_doc', $banDoc->id_doc_gia)
            ->select(
                'ms.id_muon_sach',
                'ms.id_ban_doc',
                'ms.id_dkcb',
                'ms.ngay_muon',
                'ms.han_tra',
                'ms.gia_han',
                'dkcb.ma_dkcb',
                'sach.nhan_de',
                'kap.ten_kho'
            )
            ->get();

        // Lấy thông tin mượn theo đối tượng bạn đọc
        // Giả sử sử dụng điểm lưu thông mặc định (id = 1 là kho mượn về)
        $diemLuuThongID = 1; 
        $thongTinMuon = DB::table('chi_tiet_tham_so_lt as ct')
            ->join('kho_an_pham as kho', 'ct.id_kho_an_pham', '=', 'kho.id_kho_an_pham')
            ->where('ct.id_doi_tuong_ban_doc', $doiTuongBanDoc->id_doi_tuong_ban_doc)
            ->where('ct.id_diem_luu_thong', $diemLuuThongID)
            ->select(
                'ct.id_kho_an_pham',
                'kho.ten_kho',
                'ct.muon',
                'ct.giu',
                'ct.gia_han'
            )
            ->get();

        // Tính số lượng có thể mượn tiếp
        $soLuongDangMuon = count($danhSachMuon);
        $soLuongCoTheMuon = 0;
        $soNgayMuon = 0;
        $soNgayGiaHan = 0;

        // Lấy tổng số lượng được mượn và số ngày mượn từ tất cả các kho
        foreach ($thongTinMuon as $item) {
            $soLuongCoTheMuon += $item->muon;
            $soNgayMuon = max($soNgayMuon, $item->giu);
            $soNgayGiaHan = max($soNgayGiaHan, $item->gia_han);
        }

        // Bổ sung thông tin đối tượng vào bạn đọc
        $banDoc->ten_doi_tuong_ban_doc = $doiTuongBanDoc->ten_doi_tuong_ban_doc;

        return response()->json([
            'status' => 200,
            'message' => 'Tìm thấy thông tin bạn đọc',
            'data' => [
                'ban_doc' => $banDoc,
                'thong_tin_muon' => [
                    'soLuongCoTheMuon' => $soLuongCoTheMuon - $soLuongDangMuon,
                    'soNgayMuon' => $soNgayMuon,
                    'soNgayGiaHan' => $soNgayGiaHan
                ],
                'danh_sach_dang_muon' => $danhSachMuon
            ]
        ]);
    }

    // Kiểm tra mã DKCB có thể mượn không
    public function kiemTraDKCB($maDKCB, Request $request)
    {
        $idBanDoc = $request->query('id_ban_doc');

        // Kiểm tra DKCB có tồn tại không
        $dkcb = DKCBModel::where('ma_dkcb', $maDKCB)->first();
        
        if (!$dkcb) {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy mã DKCB'
            ]);
        }

        // Kiểm tra DKCB đã được gán cho sách nào chưa
        if (!$dkcb->id_sach) {
            return response()->json([
                'status' => 400,
                'message' => 'Mã DKCB chưa được gán cho tài liệu nào'
            ]);
        }
        
        // Kiểm tra DKCB có phải thuộc kho mượn về nhà không (KM.xxxxxx hoặc id_kho_an_pham = 1)
        $isKhoMuonVe = strpos($maDKCB, 'KM.') === 0 || $dkcb->id_kho_an_pham == 1;
        
        if (!$isKhoMuonVe) {
            return response()->json([
                'status' => 400,
                'message' => 'Chỉ cho phép kiểm tra các tài liệu thuộc kho mượn về nhà'
            ]);
        }

        // Kiểm tra DKCB đã được mượn chưa (nếu tồn tại trong bảng muon_sach thì đã có người mượn)
        $daMuon = MuonSachModel::where('id_dkcb', $dkcb->id_dkcb)->exists();

        if ($daMuon) {
            return response()->json([
                'status' => 400,
                'message' => 'Tài liệu này đã được mượn'
            ]);
        }

        // Lấy thông tin sách
        $sach = SachModel::find($dkcb->id_sach);
        $kho = KhoAnPhamModel::find($dkcb->id_kho_an_pham);

        return response()->json([
            'status' => 200,
            'message' => 'Mã DKCB có thể mượn',
            'data' => [
                'id_dkcb' => $dkcb->id_dkcb,
                'ma_dkcb' => $dkcb->ma_dkcb,
                'id_sach' => $dkcb->id_sach,
                'nhan_de' => $sach ? $sach->nhan_de : 'Không xác định',
                'id_kho_an_pham' => $dkcb->id_kho_an_pham,
                'ten_kho' => $kho ? $kho->ten_kho : 'Không xác định',
                'co_the_muon' => true
            ]
        ]);
    }

    // Thực hiện mượn tài liệu
    public function muon(Request $request)
    {
        $idBanDoc = $request->input('id_ban_doc');
        $idDKCB = $request->input('id_dkcb');

        // Kiểm tra đầu vào
        if (!$idBanDoc || !$idDKCB) {
            return response()->json([
                'status' => 400,
                'message' => 'Thiếu thông tin bạn đọc hoặc mã DKCB'
            ]);
        }

        // Lấy thông tin bạn đọc
        $banDoc = DocGiaModel::find($idBanDoc);
        if (!$banDoc) {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy thông tin bạn đọc'
            ]);
        }

        // Lấy thông tin DKCB
        $dkcb = DKCBModel::find($idDKCB);
        if (!$dkcb) {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy mã DKCB'
            ]);
        }

        // Lấy thông tin đối tượng bạn đọc
        $doiTuongBanDoc = DoiTuongBanDocModel::where('ma_so_quy_uoc', $banDoc->ma_so_quy_uoc)->first();
        if (!$doiTuongBanDoc) {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy thông tin đối tượng bạn đọc'
            ]);
        }

        // Kiểm tra số lượng đang mượn
        $soLuongDangMuon = MuonSachModel::where('id_ban_doc', $idBanDoc)
            ->count();

        // Lấy thông tin mượn theo đối tượng
        $diemLuuThongID = 1; // Giả sử id=1 là kho mượn về
        $thongTinMuon = ChiTietThamSoLuuThongModel::join('kho_an_pham', 'chi_tiet_tham_so_lt.id_kho_an_pham', '=', 'kho_an_pham.id_kho_an_pham')
            ->where('chi_tiet_tham_so_lt.id_doi_tuong_ban_doc', $doiTuongBanDoc->id_doi_tuong_ban_doc)
            ->where('chi_tiet_tham_so_lt.id_diem_luu_thong', $diemLuuThongID)
            ->where('chi_tiet_tham_so_lt.id_kho_an_pham', $dkcb->id_kho_an_pham)
            ->select('chi_tiet_tham_so_lt.*')
            ->first();

        if (!$thongTinMuon) {
            return response()->json([
                'status' => 400,
                'message' => 'Đối tượng bạn đọc không được phép mượn tài liệu từ kho này'
            ]);
        }

        // Kiểm tra số lượng có thể mượn
        if ($soLuongDangMuon >= $thongTinMuon->muon) {
            return response()->json([
                'status' => 400,
                'message' => 'Bạn đọc đã mượn đủ số lượng tài liệu cho phép'
            ]);
        }

        // Tính ngày mượn và hạn trả
        $ngayMuon = Carbon::now();
        // Đảm bảo thongTinMuon->giu là số nguyên
        $soNgayGiu = (int)$thongTinMuon->giu;
        $hanTra = Carbon::now()->addDays($soNgayGiu);

        // Kiểm tra điều kiện đọc tại chỗ nếu đối tượng có giu=1 và điểm lưu thông là id=2 (đọc tại chỗ)
        if ($doiTuongBanDoc->giu == 1 && $diemLuuThongID == 2) {
            // Chỉ được mượn từ 6h sáng đến 16h30 chiều
            $gioHienTai = Carbon::now()->hour;
            $phutHienTai = Carbon::now()->minute;
            
            if ($gioHienTai > 16 || ($gioHienTai == 16 && $phutHienTai > 30)) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Đã quá thời gian cho phép mượn đọc tại chỗ (16h30)'
                ]);
            }

            // Hạn trả là cuối ngày hiện tại (16h30)
            $hanTra = Carbon::now()->setHour(16)->setMinute(30)->setSecond(0);
        }

        // Tạo phiếu mượn
        $phieuMuon = MuonSachModel::create([
            'id_ban_doc' => $idBanDoc,
            'id_dkcb' => $idDKCB,
            'ngay_muon' => $ngayMuon,
            'han_tra' => $hanTra,
            'gia_han' => 0
        ]);

        // Lấy thông tin sách
        $sach = SachModel::join('dkcb', 'sach.id_sach', '=', 'dkcb.id_sach')
            ->where('dkcb.id_dkcb', $idDKCB)
            ->select('sach.id_sach', 'sach.nhan_de')
            ->first();

        // Lấy thông tin kho
        $kho = KhoAnPhamModel::find($dkcb->id_kho_an_pham);

        return response()->json([
            'status' => 200,
            'message' => 'Mượn tài liệu thành công',
            'data' => [
                'id_muon_sach' => $phieuMuon->id_muon_sach,
                'id_ban_doc' => $phieuMuon->id_ban_doc,
                'id_dkcb' => $phieuMuon->id_dkcb,
                'ma_dkcb' => $dkcb->ma_dkcb,
                'nhan_de' => $sach ? $sach->nhan_de : 'Không xác định',
                'ten_kho' => $kho ? $kho->ten_kho : 'Không xác định',
                'ngay_muon' => $phieuMuon->ngay_muon,
                'han_tra' => $phieuMuon->han_tra,
                'gia_han' => $phieuMuon->gia_han
            ]
        ]);
    }

    // Gia hạn mượn
    public function giaHan($idMuonSach)
    {
        // Tìm phiếu mượn
        $phieuMuon = MuonSachModel::find($idMuonSach);
        
        if (!$phieuMuon) {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy phiếu mượn'
            ]);
        }

        // Kiểm tra đã gia hạn chưa
        if ($phieuMuon->gia_han > 0) {
            return response()->json([
                'status' => 400,
                'message' => 'Phiếu mượn đã được gia hạn trước đó'
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
        // Đảm bảo thongTinMuon->gia_han là số nguyên
        $soNgayGiaHan = (int)$thongTinMuon->gia_han;
        $hanTraMoi = Carbon::parse($phieuMuon->han_tra)->addDays($soNgayGiaHan);

        // Cập nhật phiếu mượn
        $phieuMuon->han_tra = $hanTraMoi;
        $phieuMuon->gia_han = 1; // Đánh dấu đã gia hạn
        $phieuMuon->save();

        return response()->json([
            'status' => 200,
            'message' => 'Gia hạn thành công',
            'data' => [
                'id_muon_sach' => $phieuMuon->id_muon_sach,
                'han_tra' => $phieuMuon->han_tra,
                'gia_han' => $phieuMuon->gia_han
            ]
        ]);
    }

    // Trả sách
    public function traSach($idMuonSach)
    {
        // Tìm phiếu mượn
        $phieuMuon = MuonSachModel::find($idMuonSach);
        
        if (!$phieuMuon) {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy phiếu mượn'
            ]);
        }

        // Xóa bản ghi mượn sách
        $phieuMuon->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Trả sách thành công',
            'data' => [
                'id_muon_sach' => $idMuonSach
            ]
        ]);
    }
}
