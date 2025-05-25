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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
}
