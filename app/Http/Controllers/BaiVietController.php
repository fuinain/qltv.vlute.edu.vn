<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BaiVietModel;
use App\Models\ChuDeBaiVietModel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BaiVietController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search ?? '';
        $perPage = $request->per_page ?? 10;
        
        $baiViet = BaiVietModel::join('chu_de_bai_viet', 'bai_viet.id_chu_de_bai_viet', '=', 'chu_de_bai_viet.id_chu_de_bai_viet')
            ->select('bai_viet.*', 'chu_de_bai_viet.ten_chu_de_bai_viet')
            ->where(function($query) use ($search) {
                if (!empty($search)) {
                    $query->where('bai_viet.ten_bai_viet', 'like', "%{$search}%")
                        ->orWhere('bai_viet.tom_tat', 'like', "%{$search}%")
                        ->orWhere('chu_de_bai_viet.ten_chu_de_bai_viet', 'like', "%{$search}%");
                }
            })
            ->orderBy('bai_viet.ngay_cap_nhat', 'desc')
            ->paginate($perPage);
            
        return response()->json([
            'status' => 200,
            'data' => $baiViet,
            'message' => 'Lấy danh sách bài viết thành công'
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ten_bai_viet' => 'required',
            'tom_tat' => 'nullable',
            'id_chu_de_bai_viet' => 'required',
            'noi_dung' => 'nullable',
            'noi_dung_navbar' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Kiểm tra tính duy nhất của noi_dung_navbar nếu không phải 'khong'
        if ($request->noi_dung_navbar && $request->noi_dung_navbar !== 'khong') {
            $exists = BaiVietModel::where('noi_dung_navbar', $request->noi_dung_navbar)->exists();
            if ($exists) {
                return response()->json([
                    'errors' => [
                        'noi_dung_navbar' => ['Đã tồn tại bài viết sử dụng nội dung navbar này']
                    ]
                ], 422);
            }
        }

        $baiViet = new BaiVietModel();
        $baiViet->ten_bai_viet = $request->ten_bai_viet;
        $baiViet->tom_tat = $request->tom_tat;
        $baiViet->noi_dung = $request->noi_dung;
        $baiViet->id_chu_de_bai_viet = $request->id_chu_de_bai_viet ?? 2;
        $baiViet->noi_dung_navbar = $request->noi_dung_navbar ?? 'khong';
        $baiViet->ngay_tao = now();
        $baiViet->ngay_cap_nhat = now();
        $baiViet->save();

        return response()->json([
            'status' => 200,
            'message' => 'Tạo bài viết thành công',
            'data' => $baiViet
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'ten_bai_viet' => 'required',
            'tom_tat' => 'nullable',
            'id_chu_de_bai_viet' => 'required',
            'noi_dung' => 'nullable',
            'noi_dung_navbar' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $baiViet = BaiVietModel::find($id);

        if (!$baiViet) {
            return response()->json(['message' => 'Không tìm thấy bài viết'], 404);
        }

        // Kiểm tra tính duy nhất của noi_dung_navbar nếu không phải 'khong' 
        // và khác với giá trị hiện tại của bài viết
        if ($request->noi_dung_navbar && $request->noi_dung_navbar !== 'khong' && 
            $request->noi_dung_navbar !== $baiViet->noi_dung_navbar) {
            $exists = BaiVietModel::where('noi_dung_navbar', $request->noi_dung_navbar)->exists();
            if ($exists) {
                return response()->json([
                    'errors' => [
                        'noi_dung_navbar' => ['Đã tồn tại bài viết sử dụng nội dung navbar này']
                    ]
                ], 422);
            }
        }

        $baiViet->ten_bai_viet = $request->ten_bai_viet;
        $baiViet->tom_tat = $request->tom_tat;
        $baiViet->noi_dung = $request->noi_dung;
        $baiViet->id_chu_de_bai_viet = $request->id_chu_de_bai_viet ?? 2;
        $baiViet->noi_dung_navbar = $request->noi_dung_navbar ?? 'khong';
        $baiViet->ngay_cap_nhat = now();
        $baiViet->save();

        return response()->json([
            'status' => 200,
            'message' => 'Cập nhật bài viết thành công',
            'data' => $baiViet
        ]);
    }

    public function destroy($id)
    {
        $baiViet = BaiVietModel::find($id);

        if (!$baiViet) {
            return response()->json(['message' => 'Không tìm thấy bài viết'], 404);
        }

        $baiViet->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Xóa bài viết thành công'
        ]);
    }

    public function getChuDeBaiViet()
    {
        $chuDeBaiViet = ChuDeBaiVietModel::all();
        return response()->json($chuDeBaiViet);
    }

    public function getBaiVietByNavbar($type)
    {
        $baiViet = BaiVietModel::where('noi_dung_navbar', $type)
            ->join('chu_de_bai_viet', 'bai_viet.id_chu_de_bai_viet', '=', 'chu_de_bai_viet.id_chu_de_bai_viet')
            ->select('bai_viet.*', 'chu_de_bai_viet.ten_chu_de_bai_viet')
            ->first();
            
        if (!$baiViet) {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy bài viết',
                'data' => null
            ]);
        }
        
        return response()->json([
            'status' => 200,
            'data' => $baiViet,
            'message' => 'Lấy bài viết thành công'
        ]);
    }

    public function getBaiVietByTenChuDe($ten_chu_de)
    {
        $chuDe = ChuDeBaiVietModel::where('ten_chu_de_bai_viet', $ten_chu_de)->first();
        
        if (!$chuDe) {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy chủ đề bài viết',
                'data' => []
            ]);
        }
        
        $baiViet = BaiVietModel::where('id_chu_de_bai_viet', $chuDe->id_chu_de_bai_viet)
            ->orderBy('ngay_tao', 'desc')
            ->limit(5)
            ->get();
            
        return response()->json([
            'status' => 200,
            'data' => $baiViet,
            'message' => 'Lấy danh sách bài viết thành công'
        ]);
    }
    
    public function getChiTietBaiViet($id)
    {
        $baiViet = BaiVietModel::find($id);
        
        if (!$baiViet) {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy bài viết',
                'data' => null
            ]);
        }
        
        return response()->json([
            'status' => 200,
            'data' => $baiViet,
            'message' => 'Lấy chi tiết bài viết thành công'
        ]);
    }
}
