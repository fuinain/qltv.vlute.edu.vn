<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DocTaiChoModel;
use App\Models\DocGiaModel;
use App\Models\DKCBModel;
use App\Models\SachModel;
use App\Models\KhoAnPhamModel;
use App\Models\DoiTuongBanDocModel;
use App\Models\ChiTietThamSoLuuThongModel;
use App\Models\BienMucBieuGhiModel;
use App\Models\BienMucTruongChaModel;
use App\Models\LichSuMuonTraModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DocTaiChoController extends Controller
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

        // Lấy danh sách tài liệu đang mượn đọc tại chỗ
        $danhSachMuon = DB::table('doc_tai_cho as dtc')
            ->join('dkcb', 'dtc.id_dkcb', '=', 'dkcb.id_dkcb')
            ->join('sach', 'dkcb.id_sach', '=', 'sach.id_sach')
            ->join('kho_an_pham as kap', 'dkcb.id_kho_an_pham', '=', 'kap.id_kho_an_pham')
            ->leftJoin('bien_muc_bieu_ghi as bmbg', 'sach.id_sach', '=', 'bmbg.id_sach')
            ->where('dtc.id_ban_doc', $banDoc->id_doc_gia)
            ->select(
                'dtc.id_doc_tai_cho',
                'dtc.id_ban_doc',
                'dtc.id_dkcb',
                'dtc.gio_muon',
                'dtc.gio_tra',
                'dkcb.ma_dkcb',
                'sach.nhan_de',
                'sach.id_sach',
                'kap.ten_kho'
            )
            ->get();

        // Lấy thông tin phân loại cho từng sách
        foreach ($danhSachMuon as $muon) {
            $phanLoai = $this->layPhanLoaiSach($muon->id_sach);
            $muon->phan_loai = $phanLoai[0] . ($phanLoai[1] ? ' / ' . $phanLoai[1] : '');
        }

        // Lấy thông tin mượn theo đối tượng bạn đọc
        // id = 2 là kho đọc tại chỗ
        $diemLuuThongID = 2; 
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

        // Lấy tổng số lượng được mượn từ tất cả các kho
        foreach ($thongTinMuon as $item) {
            $soLuongCoTheMuon += $item->muon;
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
                    'soNgayMuon' => 0, // Đọc tại chỗ không có ngày mượn, chỉ có giờ mượn đến 16h30
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
        
        // Kiểm tra DKCB có phải thuộc kho đọc tại chỗ không (KD.xxxxxx hoặc id_kho_an_pham = 2)
        $isKhoDocTaiCho = strpos($maDKCB, 'KD.') === 0 || $dkcb->id_kho_an_pham == 2;
        
        if (!$isKhoDocTaiCho) {
            return response()->json([
                'status' => 400,
                'message' => 'Chỉ cho phép kiểm tra các tài liệu thuộc kho đọc tại chỗ'
            ]);
        }

        // Kiểm tra DKCB đã được mượn chưa (nếu tồn tại trong bảng doc_tai_cho thì đã có người mượn)
        $daMuon = DocTaiChoModel::where('id_dkcb', $dkcb->id_dkcb)->exists();

        if ($daMuon) {
            return response()->json([
                'status' => 400,
                'message' => 'Tài liệu này đã được mượn'
            ]);
        }
        
        // Kiểm tra giờ hành chính
        $gioHienTai = Carbon::now()->setTimezone('Asia/Ho_Chi_Minh');
        $coTheDocTaiCho = true;
        $lyDo = '';
        
        if ($gioHienTai->hour < 6 || $gioHienTai->hour > 16 || ($gioHienTai->hour == 16 && $gioHienTai->minute > 30)) {
            $coTheDocTaiCho = false;
            $thoiGianHienTai = $gioHienTai->format('H:i');
            $lyDo = "ĐÃ NGOÀI GIỜ HÀNH CHÍNH! Hiện tại: {$thoiGianHienTai}. Chỉ cho phép mượn từ 6h00 đến 16h30.";
        }

        // Lấy thông tin sách và phân loại
        $sach = SachModel::find($dkcb->id_sach);
        $kho = KhoAnPhamModel::find($dkcb->id_kho_an_pham);
        
        $phanLoai = $this->layPhanLoaiSach($dkcb->id_sach);
        $phanLoaiText = $phanLoai[0] . ($phanLoai[1] ? ' / ' . $phanLoai[1] : '');

        return response()->json([
            'status' => 200,
            'message' => $coTheDocTaiCho ? 'Mã DKCB có thể mượn' : 'Mã DKCB không thể mượn: ' . $lyDo,
            'data' => [
                'id_dkcb' => $dkcb->id_dkcb,
                'ma_dkcb' => $dkcb->ma_dkcb,
                'id_sach' => $dkcb->id_sach,
                'nhan_de' => $sach ? $sach->nhan_de : 'Không xác định',
                'id_kho_an_pham' => $dkcb->id_kho_an_pham,
                'ten_kho' => $kho ? $kho->ten_kho : 'Không xác định',
                'phan_loai' => $phanLoaiText,
                'co_the_muon' => $coTheDocTaiCho,
                'ly_do' => $coTheDocTaiCho ? '' : $lyDo
            ]
        ]);
    }

    // Thực hiện mượn tài liệu đọc tại chỗ
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

        // Kiểm tra DKCB có phải thuộc kho đọc tại chỗ không (KD.xxxxxx hoặc id_kho_an_pham = 2)
        $isKhoDocTaiCho = strpos($dkcb->ma_dkcb, 'KD.') === 0 || $dkcb->id_kho_an_pham == 2;
        
        if (!$isKhoDocTaiCho) {
            return response()->json([
                'status' => 400,
                'message' => 'Chỉ cho phép mượn tài liệu thuộc kho đọc tại chỗ'
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
        $soLuongDangMuon = DocTaiChoModel::where('id_ban_doc', $idBanDoc)->count();

        // Lấy thông tin mượn theo đối tượng
        $diemLuuThongID = 2; // id=2 là kho đọc tại chỗ
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

        // Tính giờ mượn và hạn trả
        $gioMuon = Carbon::now()->setTimezone('Asia/Ho_Chi_Minh');
        $gioTra = Carbon::now()->setTimezone('Asia/Ho_Chi_Minh')->setHour(16)->setMinute(30)->setSecond(0);
        
        // Kiểm tra thời gian mượn phải nằm trong khoảng giờ hành chính (6h sáng đến 16h30 chiều)
        if ($gioMuon->hour < 6 || $gioMuon->hour > 16 || ($gioMuon->hour == 16 && $gioMuon->minute > 30)) {
            $thoiGianHienTai = $gioMuon->format('H:i');
            return response()->json([
                'status' => 400,
                'message' => "KHÔNG THỂ MƯỢN! Đã ngoài giờ hành chính. Hiện tại: {$thoiGianHienTai}. Chỉ cho phép mượn từ 6h00 đến 16h30."
            ]);
        }

        // Tạo phiếu mượn đọc tại chỗ
        $phieuMuon = DocTaiChoModel::create([
            'id_ban_doc' => $idBanDoc,
            'id_dkcb' => $idDKCB,
            'gio_muon' => $gioMuon,
            'gio_tra' => $gioTra,
            'qua_han' => 0
        ]);

        // Lấy thông tin sách và phân loại
        $sach = SachModel::join('dkcb', 'sach.id_sach', '=', 'dkcb.id_sach')
            ->where('dkcb.id_dkcb', $idDKCB)
            ->select('sach.id_sach', 'sach.nhan_de')
            ->first();

        $phanLoai = $this->layPhanLoaiSach($sach ? $sach->id_sach : null);
        $phanLoaiText = $phanLoai[0] . ($phanLoai[1] ? ' / ' . $phanLoai[1] : '');

        // Lấy thông tin kho
        $kho = KhoAnPhamModel::find($dkcb->id_kho_an_pham);

        return response()->json([
            'status' => 200,
            'message' => 'Mượn tài liệu thành công',
            'data' => [
                'id_doc_tai_cho' => $phieuMuon->id_doc_tai_cho,
                'id_ban_doc' => $phieuMuon->id_ban_doc,
                'id_dkcb' => $phieuMuon->id_dkcb,
                'ma_dkcb' => $dkcb->ma_dkcb,
                'nhan_de' => $sach ? $sach->nhan_de : 'Không xác định',
                'phan_loai' => $phanLoaiText,
                'ten_kho' => $kho ? $kho->ten_kho : 'Không xác định',
                'gio_muon' => $phieuMuon->gio_muon,
                'gio_tra' => $phieuMuon->gio_tra
            ]
        ]);
    }

    // Trả sách
    public function traSach($idDocTaiCho)
    {
        // Tìm phiếu mượn
        $phieuMuon = DocTaiChoModel::find($idDocTaiCho);
        
        if (!$phieuMuon) {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy phiếu mượn'
            ]);
        }

        // Lấy thông tin sách và DKCB trước khi xóa phiếu mượn
        $dkcb = DKCBModel::find($phieuMuon->id_dkcb);
        $sach = null;
        if ($dkcb) {
            $sach = SachModel::find($dkcb->id_sach);
        }

        // Lưu vào lịch sử mượn trả trước khi xóa
        LichSuMuonTraModel::create([
            'id_ban_doc' => $phieuMuon->id_ban_doc,
            'id_dkcb' => $phieuMuon->id_dkcb,
            'ma_dkcb' => $dkcb ? $dkcb->ma_dkcb : 'Không xác định',
            'ten_sach' => $sach ? $sach->nhan_de : 'Không xác định',
            'ngay_muon' => $phieuMuon->gio_muon,
            'han_tra' => $phieuMuon->gio_tra,
            'ngay_tra' => Carbon::now(),
            'tai_cho' => 1, 
            'gia_han' => 0
        ]);

        // Xóa bản ghi mượn đọc tại chỗ
        $phieuMuon->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Trả sách thành công',
            'data' => [
                'id_doc_tai_cho' => $idDocTaiCho
            ]
        ]);
    }

    // Phương thức hỗ trợ lấy phân loại sách
    private function layPhanLoaiSach($idSach)
    {
        $phanLoai = ['', ''];
        
        if (!$idSach) {
            return $phanLoai;
        }
        
        try {
            $bieuGhi = BienMucBieuGhiModel::where('id_sach', $idSach)->first();
            
            if ($bieuGhi) {
                $truong082 = BienMucTruongChaModel::where('id_bien_muc_bieu_ghi', $bieuGhi->id_bien_muc_bieu_ghi)
                    ->where('ma_truong', '082')
                    ->where('ct1', '1')
                    ->where('ct2', '4')
                    ->first();
                
                if ($truong082) {
                    $children = $truong082->children;
                    $con_a = $children->where('ma_truong_con', 'a')->first();
                    $con_b = $children->where('ma_truong_con', 'b')->first();
                    
                    if ($con_a && !empty($con_a->noi_dung)) {
                        $phanLoai[0] = $con_a->noi_dung;
                    }
                    if ($con_b && !empty($con_b->noi_dung)) {
                        $phanLoai[1] = $con_b->noi_dung;
                    }
                }
            }
        } catch (\Exception $e) {
            // Xử lý ngoại lệ nếu có
            \Log::error('Lỗi khi lấy phân loại sách: ' . $e->getMessage());
        }
        
        return $phanLoai;
    }
}
