<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DoiTuongBanDocModel;
use App\Models\ChuyenNganhModel;
use App\Models\DocGiaModel;
use App\Models\DonViModel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class DocGiaController extends Controller
{
    public function listDoiTuongBanDocForSync(Request $request)
    {
        $query = DoiTuongBanDocModel::listDoiTuongBanDocForSync();
        return response()->json([
            'status' => 200,
            'data' => $query
        ]);
    }

    public function listChuyenNganhForSync(Request $request)
    {
        $query = ChuyenNganhModel::listChuyenNganhForSync();
        return response()->json([
            'status' => 200,
            'data' => $query
        ]);
    }
    
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $perPage = $request->input('perPage', 10);
        
        $query = DocGiaModel::query()
            ->select('doc_gia.*', 'chuyen_nganh.id_don_vi', 'don_vi.ten_don_vi')
            ->leftJoin('chuyen_nganh', 'doc_gia.id_chuyen_nganh', '=', 'chuyen_nganh.id_chuyen_nganh')
            ->leftJoin('don_vi', 'chuyen_nganh.id_don_vi', '=', 'don_vi.id_don_vi');
        
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('ho_ten', 'like', '%' . $search . '%')
                  ->orWhere('mssv', 'like', '%' . $search . '%')
                  ->orWhere('ten_lop', 'like', '%' . $search . '%');
            });
        }
        
        return response()->json([
            'status' => 200,
            'data' => $query->paginate($perPage)
        ]);
    }
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ho_ten' => 'required',
            'mssv' => 'nullable|unique:doc_gia',
            'ma_lop' => 'nullable',
            'ten_lop' => 'nullable',
            'chuc_vu' => 'nullable',
            'so_the' => 'required|unique:doc_gia,so_the',
            'ngay_sinh' => 'nullable|date',
            'ngay_cap_the' => 'nullable|date',
            'han_the' => 'required|date',
            'ma_so_quy_uoc' => 'required',
            'id_chuyen_nganh' => 'required'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $docGia = DocGiaModel::create([
            'ho_ten' => $request->ho_ten,
            'mssv' => $request->mssv,
            'ma_lop' => $request->ma_lop,
            'ten_lop' => $request->ten_lop,
            'chuc_vu' => $request->chuc_vu,
            'so_the' => $request->so_the,
            'ngay_sinh' => $request->ngay_sinh,
            'ngay_cap_the' => $request->ngay_cap_the,
            'han_the' => $request->han_the,
            'lan_cap_the' => $request->lan_cap_the ?? 1,
            'ho_khau' => $request->ho_khau ?? '',
            'ghi_chu' => $request->ghi_chu ?? '',
            'rut_han' => $request->rut_han ?? 1,
            'nien_khoa' => $request->nien_khoa,
            'ma_so_quy_uoc' => $request->ma_so_quy_uoc,
            'id_chuyen_nganh' => $request->id_chuyen_nganh,
            'email' => $request->email ?? $request->mssv . '@st.vlute.edu.vn',
        ]);
        
        return response()->json([
            'status' => 200,
            'message' => 'Thêm bạn đọc thành công',
            'data' => $docGia
        ]);
    }
    
    public function show($id)
    {
        $docGia = DocGiaModel::findOrFail($id);
        return response()->json([
            'status' => 200,
            'data' => $docGia
        ]);
    }
    
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'ho_ten' => 'required',
            'mssv' => 'nullable|unique:doc_gia,mssv,' . $id . ',id_doc_gia',
            'ma_lop' => 'nullable',
            'ten_lop' => 'nullable',
            'chuc_vu' => 'nullable',
            'so_the' => 'required|unique:doc_gia,so_the,' . $id . ',id_doc_gia',
            'ngay_sinh' => 'nullable|date',
            'ngay_cap_the' => 'nullable|date',
            'han_the' => 'required|date',
            'ma_so_quy_uoc' => 'required',
            'id_chuyen_nganh' => 'required'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $docGia = DocGiaModel::findOrFail($id);
        $docGia->update([
            'ho_ten' => $request->ho_ten,
            'mssv' => $request->mssv,
            'ma_lop' => $request->ma_lop,
            'ten_lop' => $request->ten_lop,
            'chuc_vu' => $request->chuc_vu,
            'so_the' => $request->so_the,
            'ngay_sinh' => $request->ngay_sinh,
            'ngay_cap_the' => $request->ngay_cap_the,
            'han_the' => $request->han_the,
            'lan_cap_the' => $request->lan_cap_the ?? $docGia->lan_cap_the,
            'ho_khau' => $request->ho_khau ?? $docGia->ho_khau,
            'ghi_chu' => $request->ghi_chu ?? $docGia->ghi_chu,
            'rut_han' => $request->rut_han ?? $docGia->rut_han,
            'nien_khoa' => $request->nien_khoa,
            'ma_so_quy_uoc' => $request->ma_so_quy_uoc,
            'id_chuyen_nganh' => $request->id_chuyen_nganh,
            'email' => $request->email ?? $request->mssv . '@st.vlute.edu.vn',
        ]);
        
        return response()->json([
            'status' => 200,
            'message' => 'Cập nhật bạn đọc thành công',
            'data' => $docGia
        ]);
    }
    
    public function destroy($id)
    {
        $docGia = DocGiaModel::findOrFail($id);
        $docGia->delete();
        
        return response()->json([
            'status' => 200,
            'message' => 'Xóa bạn đọc thành công'
        ]);
    }
    
    public function syncBanDoc(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nam' => 'required|numeric',
            'ma_so_quy_uoc' => 'required|numeric',
            'id_chuyen_nganh' => 'required|numeric',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Thiết lập timeout để tránh ngắt kết nối
        set_time_limit(0);
        
        $nam = $request->nam;
        $maQuyUoc = $request->ma_so_quy_uoc;
        $idChuyenNganh = $request->id_chuyen_nganh;
        
        // Lấy 2 số cuối của năm
        $namFormat = substr($nam, -2);
        
        // Format id chuyên ngành thành 2 chữ số
        $idChuyenNganhFormat = str_pad($idChuyenNganh, 2, '0', STR_PAD_LEFT);
        
        $successCount = 0;
        $errorCount = 0;
        $totalProcessed = 0;
        $existingCount = 0;
        
        // Khởi tạo Guzzle HTTP Client một lần (tái sử dụng connection)
        $client = new Client([
            'base_uri' => 'http://cgtdt-dsa.vlute.edu.vn/api/',
            'headers' => [
                'Authorization' => '0d31eefa-d985-4925-805c-899b13a8492d',
                'Accept' => 'application/json'
            ],
            'http_errors' => false, // Không ném ngoại lệ cho mã HTTP lỗi
            'connect_timeout' => 10, // Timeout kết nối
            'timeout' => 30, // Timeout tổng
        ]);
        
        // Duyệt qua các MSSV từ 001 đến 999, dừng sớm nếu gặp 20 MSSV liên tiếp không tồn tại
        $maxEmpty = 20;
        $emptyCount = 0;
        for ($i = 1; $i <= 999; $i++) {
            $stt = str_pad($i, 3, '0', STR_PAD_LEFT);
            $mssv = $namFormat . $maQuyUoc . $idChuyenNganhFormat . $stt;
            $totalProcessed++;
            try {
                $response = $client->request('GET', "sinhvien/{$mssv}");
                $statusCode = $response->getStatusCode();
                if ($statusCode === 200) {
                    $responseBody = json_decode($response->getBody()->getContents(), true);
                    if (isset($responseBody['data'])) {
                        $emptyCount = 0; // reset nếu có sinh viên
                        $svData = $responseBody['data'];
                        $existingDocGia = DocGiaModel::where('mssv', $mssv)->first();
                        if (!$existingDocGia) {
                            $ngayVaoTruong = isset($svData['ngayvaotruong']) ? explode('/', $svData['ngayvaotruong']) : null;
                            $ngayRaTruong = isset($svData['ngayratruong']) ? explode('/', $svData['ngayratruong']) : null;
                            $namVao = $ngayVaoTruong ? end($ngayVaoTruong) : null;
                            $namRa = $ngayRaTruong ? end($ngayRaTruong) : null;
                            $ngayCapThe = $namVao ? $namVao . '-01-01' : null;
                            $hanThe = $namRa ? $namRa . '-12-31' : null;
                            $ngaySinh = null;
                            if (isset($svData['ngaysinh']) && !empty($svData['ngaysinh'])) {
                                $ngaySinhParts = explode('/', $svData['ngaysinh']);
                                if (count($ngaySinhParts) === 3) {
                                    $ngaySinh = $ngaySinhParts[2] . '-' . $ngaySinhParts[1] . '-' . $ngaySinhParts[0];
                                }
                            }
                            DocGiaModel::create([
                                'ho_ten' => $svData['hoten'] ?? '',
                                'mssv' => $mssv,
                                'ma_lop' => $svData['malop'] ?? '',
                                'ten_lop' => $svData['tenlop'] ?? '',
                                'so_the' => $mssv,
                                'ngay_sinh' => $ngaySinh,
                                'ngay_cap_the' => $ngayCapThe,
                                'han_the' => $hanThe,
                                'lan_cap_the' => 1,
                                'ho_khau' => $svData['diachinha'] ?? '',
                                'ghi_chu' => '',
                                'rut_han' => 1,
                                'nien_khoa' => $namVao && $namRa ? $namVao . '-' . $namRa : '',
                                'ma_so_quy_uoc' => $maQuyUoc,
                                'id_chuyen_nganh' => $idChuyenNganh,
                                'email' => $mssv . '@st.vlute.edu.vn',
                            ]);
                            $successCount++;
                        } else {
                            $existingCount++;
                        }
                    } else {
                        $emptyCount++;
                        $errorCount++;
                    }
                } else {
                    $emptyCount++;
                    $errorCount++;
                }
            } catch (RequestException $e) {
                $emptyCount++;
                $errorCount++;
            } catch (\Exception $e) {
                $emptyCount++;
                $errorCount++;
            }
            if ($emptyCount >= $maxEmpty) {
                break;
            }
            usleep(100000); // 100ms
        }
        
        // Trả về kết quả tổng hợp
        return response()->json([
            'status' => 200,
            'message' => 'Đồng bộ dữ liệu thành công',
            'data' => [
                'total' => $totalProcessed,
                'success' => $successCount,
                'error' => $errorCount,
                'existing' => $existingCount
            ],
            'note' => 'Các bạn đọc đã tồn tại được bỏ qua (không ghi đè) để giữ nguyên thông tin gia hạn thẻ do thủ thư điều chỉnh thủ công.'
        ]);
    }
}
