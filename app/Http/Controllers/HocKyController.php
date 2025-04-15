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
                            'ten_hoc_ky' => $hk['tenHK'],
                            'nam_hoc' => $hk['namHoc'],
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

    public function listKhoaHoc()
    {
        $hocKyList = HocKyModel::pluck('ma_hoc_ky')->toArray();
        $khoaHocSet = [];

        foreach ($hocKyList as $maHK) {
            $haiSoDau = substr($maHK, 0, 2);
            $khoaHoc = intval($haiSoDau) + 25;

            // Gom các khóa học duy nhất
            $khoaHocSet[$khoaHoc] = true;
        }

        $result = array_keys($khoaHocSet);
        sort($result);

        return response()->json([
            'status' => 200,
            'data' => $result
        ]);
    }

    public function listNamHoc()
    {
        $tenHocKyList = HocKyModel::pluck('ten_hoc_ky')->toArray();
        $namHocs = [];
        foreach ($tenHocKyList as $tenHK) {
            if (preg_match('/\b(\d{4}-\d{4})\b/', $tenHK, $matches)) {
                $namHocs[$matches[1]] = true;
            }
        }
        $result = array_keys($namHocs);
        sort($result);
        return response()->json([
            'status' => 200,
            'data' => $result
        ]);
    }

    public function listNienKhoa()
    {
        // Bước 1: Trích năm bắt đầu từ các chuỗi "XXXX-XXXX"
        $tenHocKyList = HocKyModel::pluck('ten_hoc_ky')->toArray();
        $years = [];

        foreach ($tenHocKyList as $tenHK) {
            if (preg_match('/\b(\d{4})-(\d{4})\b/', $tenHK, $matches)) {
                $startYear = (int)$matches[1];
                $endYear = (int)$matches[2];
                $years[] = $startYear;
                $years[] = $endYear;
            }
        }

        if (empty($years)) {
            return response()->json([
                'status' => 200,
                'data' => []
            ]);
        }

        $maxYear = max($years); // Lấy năm lớn nhất xuất hiện trong tất cả học kỳ
        $minYear = $maxYear - 7;

        $nienKhoas = [];

        // Bước 2: Sinh các niên khóa từ minYear đến maxYear, với độ dài từ 3 → 7
        for ($length = 3; $length <= 7; $length++) {
            for ($y = $minYear; $y <= $maxYear; $y++) {
                $nienKhoas[] = "{$y}-" . ($y + $length);
            }
        }

        // Xử lý: loại trùng + sắp xếp
        $nienKhoas = array_unique($nienKhoas);
        sort($nienKhoas);

        return response()->json([
            'status' => 200,
            'data' => $nienKhoas
        ]);
    }

}
