<?php

namespace App\Http\Controllers;

use App\Models\ChiTietThamSoLuuThongModel;
use App\Models\ThamSoLuuThongModel;
use Illuminate\Http\Request;

class ThamSoLuuThongController extends Controller
{
    public function index($id)
    {
        $data = ThamSoLuuThongModel::getChiTietTheoDoiTuongBanDoc($id);

        return response()->json([
            'status' => 200,
            'data' => $data,
        ]);
    }

    public function taoHoacCapNhat(Request $request)
    {
        $validated = $request->validate([
            'id_doi_tuong_ban_doc' => 'required|integer',
            'id_diem_luu_thong' => 'required|integer',
        ]);

        try {
            ChiTietThamSoLuuThongModel::taoNeuChuaCo(
                $validated['id_doi_tuong_ban_doc'],
                $validated['id_diem_luu_thong']
            );

            return response()->json(['status' => 200, 'message' => 'Đã xử lý thành công']);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Lỗi khi xử lý',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function layChiTiet($id_doi_tuong, $id_diem)
    {
        try {
            $data = ChiTietThamSoLuuThongModel::layTheoDoiTuongVaDiem($id_doi_tuong, $id_diem);

            return response()->json([
                'status' => 200,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Lỗi khi truy vấn chi tiết',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function capNhatChiTiet(Request $request)
    {
        $validated = $request->validate([
            'chi_tiet' => 'required|array',
            'chi_tiet.*.id' => 'required|integer',
            'chi_tiet.*.muon' => 'nullable|integer',
            'chi_tiet.*.giu' => 'nullable|integer',
            'chi_tiet.*.gia_han' => 'nullable|integer',
        ]);

        try {
            ChiTietThamSoLuuThongModel::capNhatNhieu($validated['chi_tiet']);
            return response()->json(['status' => 200, 'message' => 'Cập nhật thành công']);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Lỗi khi cập nhật',
                'error' => $e->getMessage(),
            ]);
        }
    }

}
