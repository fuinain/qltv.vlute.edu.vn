<?php

namespace App\Http\Controllers;

use App\Models\HocKyModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HocKyController extends Controller
{
    public function index(Request $request)
    {
    $query = HocKyModel::query();   
    return response()->json([
        'status' => 200,
        'data' => $query->paginate(perPage: 10)
    ]);
    }

    public function syncHocKy(Request $request)
{
    try {
        $response = Http::get('https://ems.vlute.edu.vn/api/danhmuc/getdshocky');
        if ($response->ok()) {
            $data = $response->json();
            if (!isset($data[0])) {
                $data = [$data];
            }
            foreach ($data as $hk) {
                HocKyModel::updateOrCreate(
                    ['ma_hoc_ky' => $hk['maHK']],
                    [ 
                        'ten_hoc_ky'  => $hk['tenHK'],
                        'nam_hoc'     => $hk['namHoc'],
                        'loai_hoc_ky' => $hk['loaiHK'],
                    ]
                );
            }
            
            return response()->json([
                'status' => 200,
                'message' => 'Đồng bộ học kỳ thành công',
            ]);
        }

        return response()->json([
            'status' => 500,
            'message' => 'Không thể kết nối đến EMS',
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status' => 500,
            'message' => 'Lỗi nội bộ: ' . $e->getMessage(),
        ]);
    }
}

}