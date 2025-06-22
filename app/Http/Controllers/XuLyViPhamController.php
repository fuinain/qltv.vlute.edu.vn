<?php

namespace App\Http\Controllers;

use App\Models\DKCBModel;
use Illuminate\Http\Request;
use App\Models\DocGiaModel;
use App\Models\XuLyViPhamModel;
use Carbon\Carbon;

class XuLyViPhamController extends Controller
{
    // Tìm kiếm bạn đọc theo MSSV
    public function timBanDoc($mssv)
    {
        try {
            // Tìm bạn đọc theo MSSV
            $banDoc = DocGiaModel::where('mssv', $mssv)
                ->orWhere('so_the', $mssv)
                ->first();

            if (!$banDoc) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Không tìm thấy thông tin bạn đọc'
                ]);
            }

            // Kiểm tra xem bạn đọc có đang bị vi phạm hay không
            $viPham = XuLyViPhamModel::where('id_ban_doc', $banDoc->id_doc_gia)
                ->where('ngay_het_han_phat', '>=', Carbon::now()->format('Y-m-d'))
                ->orderBy('ngay_phat', 'desc')
                ->first();

            // Đếm số lần vi phạm trước đó
            $soLanViPham = XuLyViPhamModel::where('id_ban_doc', $banDoc->id_doc_gia)->count();

            return response()->json([
                'status' => 200,
                'message' => 'Tìm thấy thông tin bạn đọc',
                'data' => [
                    'ban_doc' => $banDoc,
                    'dang_vi_pham' => $viPham ? true : false,
                    'thong_tin_vi_pham' => $viPham,
                    'so_lan_vi_pham' => $soLanViPham
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Lỗi khi tìm kiếm bạn đọc',
                'error' => $e->getMessage()
            ]);
        }
    }

    // Thêm vi phạm mới
    public function themViPham(Request $request)
    {
        try {
            $request->validate([
                'id_ban_doc' => 'required|integer',
                'id_phat_ban_doc' => 'required|integer',
                'ngay_het_han_phat' => 'required|date',
                'so_tien' => 'required|numeric',
                'lan_phat' => 'required|integer',
                'hinh_thuc_phat' => 'required|string',
                'ghi_chu' => 'nullable|string',
                'ma_dkcb' => 'required|string'
            ]);

            // Kiểm tra bạn đọc có tồn tại không
            $banDoc = DocGiaModel::find($request->id_ban_doc);
            if (!$banDoc) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Không tìm thấy thông tin bạn đọc'
                ]);
            }

            // Kiểm tra mã dkcb có đúng không
            $dkcb = DKCBModel::where('ma_dkcb', $request->ma_dkcb)->first();
            if (!$dkcb) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Không tìm thấy thông tin sách'
                ]);
            }


            // Thêm vi phạm mới
            $viPham = XuLyViPhamModel::create([
                'id_ban_doc' => $request->id_ban_doc,
                'id_phat_ban_doc' => $request->id_phat_ban_doc,
                'ngay_phat' => Carbon::now()->format('Y-m-d'),
                'ngay_het_han_phat' => $request->ngay_het_han_phat,
                'so_tien' => $request->so_tien,
                'hinh_thuc_phat' => $request->hinh_thuc_phat,
                'lan_phat' => $request->lan_phat,
                'id_dkcb' => $dkcb->id_dkcb,
                'ghi_chu' => $request->ghi_chu
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Thêm vi phạm thành công',
                'data' => $viPham
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Lỗi khi thêm vi phạm',
                'error' => $e->getMessage()
            ]);
        }
    }

    // Lấy danh sách vi phạm của bạn đọc
    public function danhSachViPham($mssv)
    {
        try {
            // Tìm bạn đọc theo MSSV
            $banDoc = DocGiaModel::where('mssv', $mssv)->first();

            if (!$banDoc) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Không tìm thấy thông tin bạn đọc'
                ]);
            }

            $danhSachViPham = XuLyViPhamModel::where('id_ban_doc', $banDoc->id_doc_gia)
                ->leftJoin('dkcb','dkcb.id_dkcb','xu_ly_vi_pham.id_dkcb')
                ->leftJoin('phat_ban_doc','phat_ban_doc.id_phat_ban_doc','xu_ly_vi_pham.id_phat_ban_doc')
                ->select(
                    'xu_ly_vi_pham.*',
                    'dkcb.ma_dkcb',
                    'phat_ban_doc.ten_loai_phat'
                )
                ->orderBy('ngay_phat', 'desc')
                ->get();

            return response()->json([
                'status' => 200,
                'message' => 'Lấy danh sách vi phạm thành công',
                'data' => $danhSachViPham
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Lỗi khi lấy danh sách vi phạm',
                'error' => $e->getMessage()
            ]);
        }
    }

    // Xóa vi phạm
    public function xoaViPham($id)
    {
        try {
            $viPham = XuLyViPhamModel::find($id);

            if (!$viPham) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Không tìm thấy thông tin vi phạm'
                ]);
            }

            $viPham->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Xóa vi phạm thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Lỗi khi xóa vi phạm',
                'error' => $e->getMessage()
            ]);
        }
    }
}
