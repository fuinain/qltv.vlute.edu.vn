<?php

namespace App\Http\Controllers;

use App\Models\DKCBModel;
use App\Models\KhoAnPhamModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DKCBController extends Controller
{
    /**
     * Lấy danh sách số nhãn theo kho
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function soNhan()
    {
        try {
        $soNhanArr = DKCBModel::soNhanTheoKho();

        // Lấy thông tin kho
        $khoList = KhoAnPhamModel::select('id_kho_an_pham', 'ma_kho', 'ten_kho')->get();

        // Gộp số nhãn vào từng kho
        $result = $khoList->map(function ($kho) use ($soNhanArr) {
            return [
                    'id' => $kho->id_kho_an_pham,
                'ma_kho' => $kho->ma_kho,
                'ten_kho' => $kho->ten_kho,
                    'so_hien_tai' => $soNhanArr[$kho->id_kho_an_pham] ?? 0,
            ];
        });

        return response()->json([
            'status' => 200,
            'data' => $result
        ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy số nhãn: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi lấy thông tin số nhãn'
            ]);
        }
    }
    
    /**
     * Tạo nhãn DKCB mới
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function taoNhanDKCB(Request $request)
    {
        // Validate dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'id_kho_an_pham' => 'required|exists:kho_an_pham,id_kho_an_pham',
            'so_bat_dau' => 'required|integer|min:1',
            'so_luong' => 'required|integer|min:1|max:10000',
        ], [
            'id_kho_an_pham.required' => 'Vui lòng chọn kho ấn phẩm',
            'id_kho_an_pham.exists' => 'Kho ấn phẩm không tồn tại',
            'so_bat_dau.required' => 'Vui lòng nhập số bắt đầu',
            'so_bat_dau.integer' => 'Số bắt đầu phải là số nguyên',
            'so_bat_dau.min' => 'Số bắt đầu phải lớn hơn 0',
            'so_luong.required' => 'Vui lòng nhập số lượng',
            'so_luong.integer' => 'Số lượng phải là số nguyên',
            'so_luong.min' => 'Số lượng phải lớn hơn 0',
            'so_luong.max' => 'Số lượng tối đa là 1000 nhãn mỗi lần',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $id_kho_an_pham = $request->id_kho_an_pham;
            $so_bat_dau = $request->so_bat_dau;
            $so_luong = $request->so_luong;
            
            // Kiểm tra xem nhãn đã tồn tại chưa
            $nhanTonTai = DKCBModel::where('id_kho_an_pham', $id_kho_an_pham)
                ->whereBetween('so_thu_tu', [$so_bat_dau, $so_bat_dau + $so_luong - 1])
                ->exists();
                
            if ($nhanTonTai) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Các nhãn DKCB trong khoảng này đã tồn tại, vui lòng chọn số bắt đầu khác'
                ]);
            }
            
            // Tạo nhãn DKCB mới
            $result = DKCBModel::taoNhanDKCB($id_kho_an_pham, $so_bat_dau, $so_luong);
            
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Lỗi khi tạo nhãn DKCB: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi tạo nhãn DKCB'
            ]);
        }
    }
    
    /**
     * Lấy danh sách nhãn DKCB để in
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function inNhanDKCB(Request $request)
    {
        // Validate dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'id_kho_an_pham' => 'required|exists:kho_an_pham,id_kho_an_pham',
            'so_bat_dau' => 'required|integer|min:1',
            'so_luong' => 'required|integer|min:1|max:100',
        ], [
            'id_kho_an_pham.required' => 'Vui lòng chọn kho ấn phẩm',
            'id_kho_an_pham.exists' => 'Kho ấn phẩm không tồn tại',
            'so_bat_dau.required' => 'Vui lòng nhập số bắt đầu',
            'so_bat_dau.integer' => 'Số bắt đầu phải là số nguyên',
            'so_bat_dau.min' => 'Số bắt đầu phải lớn hơn 0',
            'so_luong.required' => 'Vui lòng nhập số lượng',
            'so_luong.integer' => 'Số lượng phải là số nguyên',
            'so_luong.min' => 'Số lượng phải lớn hơn 0',
            'so_luong.max' => 'Số lượng tối đa là 100 nhãn mỗi lần',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $id_kho_an_pham = $request->id_kho_an_pham;
            $so_bat_dau = $request->so_bat_dau;
            $so_luong = $request->so_luong;
            
            // Lấy thông tin kho
            $kho = KhoAnPhamModel::find($id_kho_an_pham);
            
            if (!$kho) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Không tìm thấy thông tin kho'
                ]);
            }
            
            // Lấy danh sách nhãn cần in
            $danhSachNhan = DKCBModel::layNhanTheoBatDau($id_kho_an_pham, $so_bat_dau, $so_luong);
            
            if ($danhSachNhan->isEmpty()) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Không tìm thấy nhãn DKCB trong khoảng này'
                ]);
            }
            
            return response()->json([
                'status' => 200,
                'data' => $danhSachNhan,
                'thong_tin_kho' => [
                    'ma_kho' => $kho->ma_kho,
                    'ten_kho' => $kho->ten_kho
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy nhãn DKCB để in: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi lấy nhãn DKCB để in'
            ]);
        }
    }

    /**
     * Lấy danh sách nhãn theo kho
     * 
     * @param  int  $id_kho
     * @return \Illuminate\Http\JsonResponse
     */
    public function danhSachNhanTheoKho($id_kho)
    {
        try {
            // Kiểm tra kho tồn tại
            $kho = KhoAnPhamModel::find($id_kho);
            
            if (!$kho) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Không tìm thấy thông tin kho'
                ]);
            }
            
            // Lấy danh sách nhãn DKCB của kho được chọn và thông tin sách liên quan
            $danhSachNhan = \App\Models\DKCBModel::where('id_kho_an_pham', $id_kho)
                ->where('trang_thai', 1)
                ->orderBy('so_thu_tu', 'asc')
                ->leftJoin('sach', 'dkcb.id_sach', '=', 'sach.id_sach') // Join với bảng sách
                ->select('dkcb.id_dkcb as id', 'dkcb.ma_dkcb', 'dkcb.so_thu_tu', 'sach.nhan_de as ten_sach') // Thêm select tên sách
                ->get();

            return response()->json([
                'status' => 200,
                'data' => $danhSachNhan
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy danh sách nhãn theo kho: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi lấy danh sách nhãn: ' . $e->getMessage()
            ], 500);
        }
    }
}
