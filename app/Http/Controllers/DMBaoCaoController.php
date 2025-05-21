<?php

namespace App\Http\Controllers;

use App\Models\DMBaoCaoModel;
use App\Models\MuonSachModel;
use App\Models\DocTaiChoModel;
use App\Models\DKCBModel;
use App\Models\SachModel;
use App\Models\DocGiaModel;
use App\Models\ChuyenNganhModel;
use App\Models\DonViModel;
use App\Models\LichSuMuonTraModel;
use App\Models\CheckinBanDocModel;
use App\Models\DoiTuongBanDocModel;
use App\Models\XuLyViPhamModel;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\DB;

class DMBaoCaoController extends Controller
{
    public function index()
    {
        try {
            $danhSachBaoCao = DMBaoCaoModel::all();
        return response()->json([
            'status' => 200,
                'message' => 'Lấy danh sách báo cáo thành công',
                'data' => $danhSachBaoCao
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Lỗi khi lấy danh sách báo cáo',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'ten_bao_cao' => 'required|string|max:255',
            ]);

            $baoCao = DMBaoCaoModel::create([
                'ten_bao_cao' => $request->ten_bao_cao,
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Thêm báo cáo thành công',
                'data' => $baoCao
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Lỗi khi thêm báo cáo',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'ten_bao_cao' => 'required|string|max:255',
            ]);

            $baoCao = DMBaoCaoModel::find($id);
            
            if (!$baoCao) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Không tìm thấy báo cáo'
                ]);
            }

            $baoCao->update([
                'ten_bao_cao' => $request->ten_bao_cao,
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Cập nhật báo cáo thành công',
                'data' => $baoCao
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Lỗi khi cập nhật báo cáo',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            $baoCao = DMBaoCaoModel::find($id);
            
            if (!$baoCao) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Không tìm thấy báo cáo'
                ]);
            }

            $baoCao->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Xóa báo cáo thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Lỗi khi xóa báo cáo',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function listDMBaoCaoSelectOption()
    {
        try {
            $danhSachBaoCao = DMBaoCaoModel::all();
        return response()->json([
            'status' => 200,
                'message' => 'Lấy danh sách báo cáo thành công',
                'data' => $danhSachBaoCao
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Lỗi khi lấy danh sách báo cáo',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function thongKeSachDangMuon(Request $request)
    {
        try {
            $request->validate([
                'tu_ngay' => 'required|date',
                'den_ngay' => 'required|date|after_or_equal:tu_ngay',
            ]);

            $tuNgay = Carbon::parse($request->tu_ngay)->startOfDay();
            $denNgay = Carbon::parse($request->den_ngay)->endOfDay();

            // Lấy dữ liệu từ bảng muon_sach - tất cả sách trong bảng là sách đang mượn
            $muonSach = MuonSachModel::where(function($query) use ($tuNgay, $denNgay) {
                    $query->whereBetween('ngay_muon', [$tuNgay, $denNgay])
                        ->orWhereBetween('han_tra', [$tuNgay, $denNgay]);
                })
                ->get();

            // Lấy dữ liệu từ bảng doc_tai_cho - tất cả sách trong bảng là đang đọc tại chỗ
            $docTaiCho = DocTaiChoModel::where(function($query) use ($tuNgay, $denNgay) {
                    $query->whereBetween('gio_muon', [$tuNgay, $denNgay]);
                })
                ->get();

            $ketQua = [];

            // Xử lý dữ liệu từ bảng muon_sach
            foreach ($muonSach as $item) {
                $dkcb = DKCBModel::find($item->id_dkcb);
                if (!$dkcb) continue;

                $sach = SachModel::find($dkcb->id_sach);
                if (!$sach) continue;

                $docGia = DocGiaModel::find($item->id_ban_doc);
                if (!$docGia) continue;

                $chuyenNganh = null;
                $donVi = null;

                if ($docGia->id_chuyen_nganh) {
                    $chuyenNganh = ChuyenNganhModel::find($docGia->id_chuyen_nganh);
                    if ($chuyenNganh && $chuyenNganh->id_don_vi) {
                        $donVi = DonViModel::find($chuyenNganh->id_don_vi);
                    }
                }

                $donViQuanLy = '';
                if ($donVi && $chuyenNganh) {
                    $donViQuanLy = $donVi->ten_don_vi . ' - ' . $chuyenNganh->ten_chuyen_nganh;
                } elseif ($chuyenNganh) {
                    $donViQuanLy = $chuyenNganh->ten_chuyen_nganh;
                }

                $ketQua[] = [
                    'id_dkcb' => $dkcb->id_dkcb,
                    'ma_dkcb' => $dkcb->ma_dkcb,
                    'nhan_de' => $sach->nhan_de,
                    'so_the' => $docGia->so_the,
                    'ho_ten' => $docGia->ho_ten,
                    'don_vi_quan_ly' => $donViQuanLy,
                    'ngay_muon' => $item->ngay_muon,
                    'han_tra' => $item->han_tra,
                    'tai_cho' => false,
                    'nguon' => 'muon_sach',
                    'id_nguon' => $item->id_muon_sach
                ];
            }

            // Xử lý dữ liệu từ bảng doc_tai_cho
            foreach ($docTaiCho as $item) {
                $dkcb = DKCBModel::find($item->id_dkcb);
                if (!$dkcb) continue;

                $sach = SachModel::find($dkcb->id_sach);
                if (!$sach) continue;

                $docGia = DocGiaModel::find($item->id_ban_doc);
                if (!$docGia) continue;

                $chuyenNganh = null;
                $donVi = null;

                if ($docGia->id_chuyen_nganh) {
                    $chuyenNganh = ChuyenNganhModel::find($docGia->id_chuyen_nganh);
                    if ($chuyenNganh && $chuyenNganh->id_don_vi) {
                        $donVi = DonViModel::find($chuyenNganh->id_don_vi);
                    }
                }

                $donViQuanLy = '';
                if ($donVi && $chuyenNganh) {
                    $donViQuanLy = $donVi->ten_don_vi . ' - ' . $chuyenNganh->ten_chuyen_nganh;
                } elseif ($chuyenNganh) {
                    $donViQuanLy = $chuyenNganh->ten_chuyen_nganh;
                }

                $ketQua[] = [
                    'id_dkcb' => $dkcb->id_dkcb,
                    'ma_dkcb' => $dkcb->ma_dkcb,
                    'nhan_de' => $sach->nhan_de,
                    'so_the' => $docGia->so_the,
                    'ho_ten' => $docGia->ho_ten,
                    'don_vi_quan_ly' => $donViQuanLy,
                    'ngay_muon' => $item->gio_muon,
                    'han_tra' => $item->gio_tra,
                    'tai_cho' => true,
                    'nguon' => 'doc_tai_cho',
                    'id_nguon' => $item->id_doc_tai_cho
                ];
            }

            return response()->json([
                'status' => 200,
                'message' => 'Lấy thống kê sách đang mượn thành công',
                'data' => $ketQua
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Lỗi khi lấy thống kê sách đang mượn',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function xuatExcelSachDangMuon(Request $request)
    {
        try {
            $validated = $this->validate($request, [
                'tu_ngay' => 'required|date',
                'den_ngay' => 'required|date|after_or_equal:tu_ngay',
            ], [
                'tu_ngay.required' => 'Vui lòng chọn ngày bắt đầu',
                'den_ngay.required' => 'Vui lòng chọn ngày kết thúc',
                'den_ngay.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu',
            ]);

            $tuNgay = Carbon::parse($validated['tu_ngay'])->startOfDay();
            $denNgay = Carbon::parse($validated['den_ngay'])->endOfDay();

            // Lấy dữ liệu từ bảng muon_sach - tất cả sách trong bảng là sách đang mượn
            $muonSach = MuonSachModel::where(function($query) use ($tuNgay, $denNgay) {
                    $query->whereBetween('ngay_muon', [$tuNgay, $denNgay])
                        ->orWhereBetween('han_tra', [$tuNgay, $denNgay]);
                })
                ->get();

            // Lấy dữ liệu từ bảng doc_tai_cho - tất cả sách trong bảng là đang đọc tại chỗ
            $docTaiCho = DocTaiChoModel::where(function($query) use ($tuNgay, $denNgay) {
                    $query->whereBetween('gio_muon', [$tuNgay, $denNgay]);
                })
                ->get();

            $ketQua = [];

            // Xử lý dữ liệu từ bảng muon_sach
            foreach ($muonSach as $item) {
                $dkcb = DKCBModel::find($item->id_dkcb);
                if (!$dkcb) continue;

                $sach = SachModel::find($dkcb->id_sach);
                if (!$sach) continue;

                $docGia = DocGiaModel::find($item->id_ban_doc);
                if (!$docGia) continue;

                $chuyenNganh = null;
                $donVi = null;

                if ($docGia->id_chuyen_nganh) {
                    $chuyenNganh = ChuyenNganhModel::find($docGia->id_chuyen_nganh);
                    if ($chuyenNganh && $chuyenNganh->id_don_vi) {
                        $donVi = DonViModel::find($chuyenNganh->id_don_vi);
                    }
                }

                $donViQuanLy = '';
                if ($donVi && $chuyenNganh) {
                    $donViQuanLy = $donVi->ten_don_vi . ' - ' . $chuyenNganh->ten_chuyen_nganh;
                } elseif ($chuyenNganh) {
                    $donViQuanLy = $chuyenNganh->ten_chuyen_nganh;
                }

                $ketQua[] = [
                    'id_dkcb' => $dkcb->id_dkcb,
                    'ma_dkcb' => $dkcb->ma_dkcb,
                    'nhan_de' => $sach->nhan_de,
                    'so_the' => $docGia->so_the,
                    'ho_ten' => $docGia->ho_ten,
                    'don_vi_quan_ly' => $donViQuanLy,
                    'ngay_muon' => $item->ngay_muon,
                    'han_tra' => $item->han_tra,
                    'tai_cho' => false,
                    'nguon' => 'muon_sach',
                    'id_nguon' => $item->id_muon_sach
                ];
            }

            // Xử lý dữ liệu từ bảng doc_tai_cho
            foreach ($docTaiCho as $item) {
                $dkcb = DKCBModel::find($item->id_dkcb);
                if (!$dkcb) continue;

                $sach = SachModel::find($dkcb->id_sach);
                if (!$sach) continue;

                $docGia = DocGiaModel::find($item->id_ban_doc);
                if (!$docGia) continue;

                $chuyenNganh = null;
                $donVi = null;

                if ($docGia->id_chuyen_nganh) {
                    $chuyenNganh = ChuyenNganhModel::find($docGia->id_chuyen_nganh);
                    if ($chuyenNganh && $chuyenNganh->id_don_vi) {
                        $donVi = DonViModel::find($chuyenNganh->id_don_vi);
                    }
                }

                $donViQuanLy = '';
                if ($donVi && $chuyenNganh) {
                    $donViQuanLy = $donVi->ten_don_vi . ' - ' . $chuyenNganh->ten_chuyen_nganh;
                } elseif ($chuyenNganh) {
                    $donViQuanLy = $chuyenNganh->ten_chuyen_nganh;
                }

                $ketQua[] = [
                    'id_dkcb' => $dkcb->id_dkcb,
                    'ma_dkcb' => $dkcb->ma_dkcb,
                    'nhan_de' => $sach->nhan_de,
                    'so_the' => $docGia->so_the,
                    'ho_ten' => $docGia->ho_ten,
                    'don_vi_quan_ly' => $donViQuanLy,
                    'ngay_muon' => $item->gio_muon,
                    'han_tra' => $item->gio_tra,
                    'tai_cho' => true,
                    'nguon' => 'doc_tai_cho',
                    'id_nguon' => $item->id_doc_tai_cho
                ];
            }

            // Xuất Excel dựa trên template
            $templatePath = public_path('template_excel/template_excel_bctk_sach_dang_muon.xlsx');
            if (!file_exists($templatePath)) {
                return response()->json([
                    'status' => 500,
                    'message' => 'Không tìm thấy file mẫu báo cáo'
                ], 500);
            }

            // Tạo đối tượng PhpSpreadsheet từ file mẫu
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($templatePath);
            $sheet = $spreadsheet->getActiveSheet();

            // Thiết lập font chữ Times New Roman và kích thước 13 cho toàn bộ sheet
            $defaultFont = [
                'font' => [
                    'name' => 'Times New Roman',
                    'size' => 13,
                ]
            ];
            $sheet->getParent()->getDefaultStyle()->applyFromArray($defaultFont);

            // Cài đặt thông tin chung - Từ ngày đến ngày
            $tuNgayFormatted = Carbon::parse($validated['tu_ngay'])->format('d-m-Y');
            $denNgayFormatted = Carbon::parse($validated['den_ngay'])->format('d-m-Y');
            $sheet->setCellValue('A5', "Từ ngày: {$tuNgayFormatted} Đến ngày: {$denNgayFormatted}");

            // Chuẩn bị kiểu border
            $borderStyle = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ];

            // Điền dữ liệu sách 
            $startRow = 8;
            $stt = 1;
            $currentRow = $startRow;

            foreach ($ketQua as $item) {
                // Điền dữ liệu vào hàng hiện tại
                $sheet->setCellValue('A' . $currentRow, $stt);
                $sheet->setCellValue('B' . $currentRow, $item['ma_dkcb']);
                $sheet->setCellValue('C' . $currentRow, $item['nhan_de']);
                $sheet->setCellValue('D' . $currentRow, $item['so_the']);
                $sheet->setCellValue('E' . $currentRow, $item['ho_ten']);
                $sheet->setCellValue('F' . $currentRow, $item['don_vi_quan_ly']);
                $sheet->setCellValue('G' . $currentRow, Carbon::parse($item['ngay_muon'])->format('d/m/Y'));
                $sheet->setCellValue('H' . $currentRow, $item['han_tra'] ? Carbon::parse($item['han_tra'])->format('d/m/Y') : '');
                $sheet->setCellValue('I' . $currentRow, $item['tai_cho'] ? 'X' : '');
                
                // Căn giữa cột STT và cột Tại chỗ
                $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('I' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Style cho hàng
                $sheet->getStyle('A' . $currentRow . ':I' . $currentRow)->applyFromArray($borderStyle);
                $sheet->getStyle('A' . $currentRow . ':I' . $currentRow)->applyFromArray($defaultFont);
                
                // Tăng số thứ tự và chuyển sang hàng tiếp theo
                $stt++;
                $currentRow++;
            }
            
            // Thêm dòng tổng cộng
            $tongSoSach = count($ketQua);
            $sheet->setCellValue('A' . $currentRow, 'TC');
            $sheet->setCellValue('B' . $currentRow, $tongSoSach);
            
            // Định dạng dòng tổng cộng
            $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
            $sheet->getStyle('B' . $currentRow)->getFont()->setBold(true);
            $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
            // Style cho hàng tổng cộng
            $sheet->getStyle('A' . $currentRow . ':I' . $currentRow)->applyFromArray($borderStyle);
            $sheet->getStyle('A' . $currentRow . ':I' . $currentRow)->applyFromArray($defaultFont);
            
            // Thêm thông tin người lập báo cáo ở cuối
            $currentRow += 2;
            $sheet->setCellValue('G' . $currentRow, "Vĩnh Long, ngày " . Carbon::now()->day . " tháng " . Carbon::now()->month . " năm " . Carbon::now()->year);
            $sheet->mergeCells('G' . $currentRow . ':I' . $currentRow);
            $sheet->getStyle('G' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('G' . $currentRow)->applyFromArray($defaultFont);
            
            $currentRow += 1;
            $sheet->setCellValue('G' . $currentRow, "Người lập báo cáo");
            $sheet->mergeCells('G' . $currentRow . ':I' . $currentRow);
            $sheet->getStyle('G' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('G' . $currentRow)->getFont()->setBold(true);
            $sheet->getStyle('G' . $currentRow)->applyFromArray($defaultFont);
            
            $currentRow += 4;
            $sheet->setCellValue('G' . $currentRow, session('HoTen') ?? '');
            $sheet->mergeCells('G' . $currentRow . ':I' . $currentRow);
            $sheet->getStyle('G' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('G' . $currentRow)->applyFromArray($defaultFont);
            
            // Tên file xuất 
            $fileName = 'BC_Sach_Dang_Muon_' . Carbon::now()->format('dmY_His') . '.xlsx';
            
            // Trả về file Excel
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
            
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Lỗi xuất báo cáo sách đang mượn: ' . $e->getMessage());
            return response()->json([
                'status' => 500, 
                'message' => 'Đã xảy ra lỗi khi xuất báo cáo: ' . $e->getMessage()
            ], 500);
        }
    }


    public function thongKeSachDaTra(Request $request)
    {
        try {
            $request->validate([
                'tu_ngay' => 'required|date',
                'den_ngay' => 'required|date|after_or_equal:tu_ngay',
            ]);

            $tuNgay = Carbon::parse($request->tu_ngay)->startOfDay();
            $denNgay = Carbon::parse($request->den_ngay)->endOfDay();

            // Lấy dữ liệu từ bảng lịch sử mượn trả - những bản ghi có ngày trả
            $lichSuMuonTra = LichSuMuonTraModel::whereNotNull('ngay_tra')
                ->whereBetween('ngay_tra', [$tuNgay, $denNgay])
                ->get();

            $ketQua = [];

            // Xử lý dữ liệu từ bảng lich_su_muon_tra
            foreach ($lichSuMuonTra as $lichSu) {
                $docGia = DocGiaModel::find($lichSu->id_ban_doc);
                if (!$docGia) continue;

                $chuyenNganh = null;
                $donVi = null;

                if ($docGia->id_chuyen_nganh) {
                    $chuyenNganh = ChuyenNganhModel::find($docGia->id_chuyen_nganh);
                    if ($chuyenNganh && $chuyenNganh->id_don_vi) {
                        $donVi = DonViModel::find($chuyenNganh->id_don_vi);
                    }
                }

                $donViQuanLy = '';
                if ($donVi && $chuyenNganh) {
                    $donViQuanLy = $donVi->ten_don_vi . ' - ' . $chuyenNganh->ten_chuyen_nganh;
                } elseif ($chuyenNganh) {
                    $donViQuanLy = $chuyenNganh->ten_chuyen_nganh;
                }

                $ketQua[] = [
                    'id_lich_su' => $lichSu->id_lich_su,
                    'id_dkcb' => $lichSu->id_dkcb,
                    'ma_dkcb' => $lichSu->ma_dkcb,
                    'nhan_de' => $lichSu->ten_sach,
                    'so_the' => $docGia->so_the,
                    'ho_ten' => $docGia->ho_ten,
                    'don_vi_quan_ly' => $donViQuanLy,
                    'ngay_muon' => $lichSu->ngay_muon,
                    'han_tra' => $lichSu->han_tra,
                    'ngay_tra' => $lichSu->ngay_tra,
                    'tai_cho' => $lichSu->tai_cho == 1, // Convert to boolean
                    'gia_han' => $lichSu->gia_han
                ];
            }

            return response()->json([
                'status' => 200,
                'message' => 'Lấy thống kê sách đã trả thành công',
                'data' => $ketQua
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Lỗi khi lấy thống kê sách đã trả',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function xuatExcelSachDaTra(Request $request)
    {
        try {
            $validated = $this->validate($request, [
                'tu_ngay' => 'required|date',
                'den_ngay' => 'required|date|after_or_equal:tu_ngay',
            ], [
                'tu_ngay.required' => 'Vui lòng chọn ngày bắt đầu',
                'den_ngay.required' => 'Vui lòng chọn ngày kết thúc',
                'den_ngay.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu',
            ]);

            $tuNgay = Carbon::parse($validated['tu_ngay'])->startOfDay();
            $denNgay = Carbon::parse($validated['den_ngay'])->endOfDay();

            // Lấy dữ liệu từ bảng lịch sử mượn trả - những bản ghi có ngày trả
            $lichSuMuonTra = LichSuMuonTraModel::whereNotNull('ngay_tra')
                ->whereBetween('ngay_tra', [$tuNgay, $denNgay])
                ->get();

            $ketQua = [];

            // Xử lý dữ liệu từ bảng lich_su_muon_tra
            foreach ($lichSuMuonTra as $lichSu) {
                $docGia = DocGiaModel::find($lichSu->id_ban_doc);
                if (!$docGia) continue;

                $chuyenNganh = null;
                $donVi = null;

                if ($docGia->id_chuyen_nganh) {
                    $chuyenNganh = ChuyenNganhModel::find($docGia->id_chuyen_nganh);
                    if ($chuyenNganh && $chuyenNganh->id_don_vi) {
                        $donVi = DonViModel::find($chuyenNganh->id_don_vi);
                    }
                }

                $donViQuanLy = '';
                if ($donVi && $chuyenNganh) {
                    $donViQuanLy = $donVi->ten_don_vi . ' - ' . $chuyenNganh->ten_chuyen_nganh;
                } elseif ($chuyenNganh) {
                    $donViQuanLy = $chuyenNganh->ten_chuyen_nganh;
                }

                $ketQua[] = [
                    'id_lich_su' => $lichSu->id_lich_su,
                    'ma_dkcb' => $lichSu->ma_dkcb,
                    'nhan_de' => $lichSu->ten_sach,
                    'so_the' => $docGia->so_the,
                    'ho_ten' => $docGia->ho_ten,
                    'don_vi_quan_ly' => $donViQuanLy,
                    'ngay_muon' => $lichSu->ngay_muon,
                    'han_tra' => $lichSu->han_tra,
                    'ngay_tra' => $lichSu->ngay_tra,
                    'tai_cho' => $lichSu->tai_cho
                ];
            }

            // Xuất Excel dựa trên template
            $templatePath = public_path('template_excel/template_excel_bctk_sach_da_tra.xlsx');
            if (!file_exists($templatePath)) {
                return response()->json([
                    'status' => 500,
                    'message' => 'Không tìm thấy file mẫu báo cáo'
                ], 500);
            }

            // Tạo đối tượng PhpSpreadsheet từ file mẫu
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($templatePath);
            $sheet = $spreadsheet->getActiveSheet();

            // Thiết lập font chữ Times New Roman và kích thước 13 cho toàn bộ sheet
            $defaultFont = [
                'font' => [
                    'name' => 'Times New Roman',
                    'size' => 13,
                ]
            ];
            $sheet->getParent()->getDefaultStyle()->applyFromArray($defaultFont);

            // Cài đặt thông tin chung - Từ ngày đến ngày
            $tuNgayFormatted = Carbon::parse($validated['tu_ngay'])->format('d-m-Y');
            $denNgayFormatted = Carbon::parse($validated['den_ngay'])->format('d-m-Y');
            $sheet->setCellValue('A5', "Từ ngày: {$tuNgayFormatted} Đến ngày: {$denNgayFormatted}");

            // Chuẩn bị kiểu border
            $borderStyle = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ];

            // Điền dữ liệu sách 
            $startRow = 8;
            $stt = 1;
            $currentRow = $startRow;

            foreach ($ketQua as $item) {
                // Điền dữ liệu vào hàng hiện tại
                $sheet->setCellValue('A' . $currentRow, $stt);
                $sheet->setCellValue('B' . $currentRow, $item['ma_dkcb']);
                $sheet->setCellValue('C' . $currentRow, $item['nhan_de']);
                $sheet->setCellValue('D' . $currentRow, $item['so_the']);
                $sheet->setCellValue('E' . $currentRow, $item['ho_ten']);
                $sheet->setCellValue('F' . $currentRow, Carbon::parse($item['ngay_muon'])->format('d/m/Y'));
                $sheet->setCellValue('G' . $currentRow, Carbon::parse($item['han_tra'])->format('d/m/Y'));
                $sheet->setCellValue('H' . $currentRow, Carbon::parse($item['ngay_tra'])->format('d/m/Y'));
                $sheet->setCellValue('I' . $currentRow, $item['tai_cho'] == 1 ? 'X' : '');
                
                // Căn giữa cột STT và cột Tại chỗ
                $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('I' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Style cho hàng
                $sheet->getStyle('A' . $currentRow . ':I' . $currentRow)->applyFromArray($borderStyle);
                $sheet->getStyle('A' . $currentRow . ':I' . $currentRow)->applyFromArray($defaultFont);
                
                // Tăng số thứ tự và chuyển sang hàng tiếp theo
                $stt++;
                $currentRow++;
            }
            
            // Thêm dòng tổng cộng
            $tongSoSach = count($ketQua);
            $sheet->setCellValue('A' . $currentRow, 'TC');
            $sheet->setCellValue('B' . $currentRow, $tongSoSach);
            
            // Định dạng dòng tổng cộng
            $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
            $sheet->getStyle('B' . $currentRow)->getFont()->setBold(true);
            $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
            // Style cho hàng tổng cộng
            $sheet->getStyle('A' . $currentRow . ':I' . $currentRow)->applyFromArray($borderStyle);
            $sheet->getStyle('A' . $currentRow . ':I' . $currentRow)->applyFromArray($defaultFont);
            
            // Thêm thông tin người lập báo cáo ở cuối
            $currentRow += 2;
            $sheet->setCellValue('F' . $currentRow, "Vĩnh Long, ngày " . Carbon::now()->day . " tháng " . Carbon::now()->month . " năm " . Carbon::now()->year);
            $sheet->mergeCells('F' . $currentRow . ':I' . $currentRow);
            $sheet->getStyle('F' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $currentRow)->applyFromArray($defaultFont);
            
            $currentRow += 1;
            $sheet->setCellValue('F' . $currentRow, "Người lập báo cáo");
            $sheet->mergeCells('F' . $currentRow . ':I' . $currentRow);
            $sheet->getStyle('F' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $currentRow)->getFont()->setBold(true);
            $sheet->getStyle('F' . $currentRow)->applyFromArray($defaultFont);
            
            $currentRow += 4;
            $sheet->setCellValue('F' . $currentRow, session('HoTen') ?? '');
            $sheet->mergeCells('F' . $currentRow . ':I' . $currentRow);
            $sheet->getStyle('F' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $currentRow)->applyFromArray($defaultFont);
            
            // Tên file xuất 
            $fileName = 'BC_Sach_Da_Tra_' . Carbon::now()->format('dmY_His') . '.xlsx';
            
            // Trả về file Excel
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
            
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Lỗi xuất báo cáo sách đã trả: ' . $e->getMessage());
            return response()->json([
                'status' => 500, 
                'message' => 'Đã xảy ra lỗi khi xuất báo cáo: ' . $e->getMessage()
            ], 500);
        }
    }

    public function thongKeSachQuaHan(Request $request)
    {
        try {
            $request->validate([
                'tu_ngay' => 'required|date',
                'den_ngay' => 'required|date|after_or_equal:tu_ngay',
            ]);

            $tuNgay = Carbon::parse($request->tu_ngay)->startOfDay();
            $denNgay = Carbon::parse($request->den_ngay)->endOfDay();
            $ngayHienTai = Carbon::now();

            // Lấy sách mượn quá hạn
            $muonSachQuaHan = MuonSachModel::where('qua_han', 1)
                ->where(function($query) use ($tuNgay, $denNgay) {
                    $query->whereBetween('ngay_muon', [$tuNgay, $denNgay])
                        ->orWhereBetween('han_tra', [$tuNgay, $denNgay]);
                })
                ->get();

            // Lấy sách đọc tại chỗ quá hạn
            $docTaiChoQuaHan = DocTaiChoModel::where('qua_han', 1)
                ->where(function($query) use ($tuNgay, $denNgay) {
                    $query->whereBetween('gio_muon', [$tuNgay, $denNgay]);
                })
                ->get();

            $ketQua = [];

            // Xử lý dữ liệu sách mượn quá hạn
            foreach ($muonSachQuaHan as $item) {
                $dkcb = DKCBModel::find($item->id_dkcb);
                if (!$dkcb) continue;

                $sach = SachModel::find($dkcb->id_sach);
                if (!$sach) continue;

                $docGia = DocGiaModel::find($item->id_ban_doc);
                if (!$docGia) continue;

                $chuyenNganh = null;
                $donVi = null;

                if ($docGia->id_chuyen_nganh) {
                    $chuyenNganh = ChuyenNganhModel::find($docGia->id_chuyen_nganh);
                    if ($chuyenNganh && $chuyenNganh->id_don_vi) {
                        $donVi = DonViModel::find($chuyenNganh->id_don_vi);
                    }
                }

                $donViQuanLy = '';
                if ($donVi && $chuyenNganh) {
                    $donViQuanLy = $donVi->ten_don_vi . ' - ' . $chuyenNganh->ten_chuyen_nganh;
                } elseif ($chuyenNganh) {
                    $donViQuanLy = $chuyenNganh->ten_chuyen_nganh;
                }

                // Tính số ngày trễ cho sách mượn bằng cách lấy timestamp và tính chênh lệch ngày
                $hanTra = Carbon::parse($item->han_tra);
                $soNgayTre = 0;
                if ($ngayHienTai > $hanTra) {
                    // Chuyển về timestamps (seconds) rồi chuyển thành ngày
                    $chenhLechGiay = $ngayHienTai->timestamp - $hanTra->timestamp;
                    $soNgayTre = floor($chenhLechGiay / (60 * 60 * 24));
                }

                $ketQua[] = [
                    'id_dkcb' => $dkcb->id_dkcb,
                    'ma_dkcb' => $dkcb->ma_dkcb,
                    'nhan_de' => $sach->nhan_de,
                    'so_the' => $docGia->so_the,
                    'ho_ten' => $docGia->ho_ten,
                    'don_vi_quan_ly' => $donViQuanLy,
                    'ngay_muon' => $item->ngay_muon,
                    'han_tra' => $item->han_tra,
                    'so_ngay_tre' => $soNgayTre,
                    'tai_cho' => false,
                    'nguon' => 'muon_sach',
                    'id_nguon' => $item->id_muon_sach
                ];
            }

            // Xử lý dữ liệu sách đọc tại chỗ quá hạn
            foreach ($docTaiChoQuaHan as $item) {
                $dkcb = DKCBModel::find($item->id_dkcb);
                if (!$dkcb) continue;

                $sach = SachModel::find($dkcb->id_sach);
                if (!$sach) continue;

                $docGia = DocGiaModel::find($item->id_ban_doc);
                if (!$docGia) continue;

                $chuyenNganh = null;
                $donVi = null;

                if ($docGia->id_chuyen_nganh) {
                    $chuyenNganh = ChuyenNganhModel::find($docGia->id_chuyen_nganh);
                    if ($chuyenNganh && $chuyenNganh->id_don_vi) {
                        $donVi = DonViModel::find($chuyenNganh->id_don_vi);
                    }
                }

                $donViQuanLy = '';
                if ($donVi && $chuyenNganh) {
                    $donViQuanLy = $donVi->ten_don_vi . ' - ' . $chuyenNganh->ten_chuyen_nganh;
                } elseif ($chuyenNganh) {
                    $donViQuanLy = $chuyenNganh->ten_chuyen_nganh;
                }

                // Tính số ngày trễ cho đọc tại chỗ bằng cách lấy timestamp và tính chênh lệch ngày
                $gioTra = Carbon::parse($item->gio_tra);
                $soNgayTre = 0;
                if ($ngayHienTai > $gioTra) {
                    // Chuyển về timestamps (seconds) rồi chuyển thành ngày
                    $chenhLechGiay = $ngayHienTai->timestamp - $gioTra->timestamp;
                    $soNgayTre = floor($chenhLechGiay / (60 * 60 * 24));
                }

                $ketQua[] = [
                    'id_dkcb' => $dkcb->id_dkcb,
                    'ma_dkcb' => $dkcb->ma_dkcb,
                    'nhan_de' => $sach->nhan_de,
                    'so_the' => $docGia->so_the,
                    'ho_ten' => $docGia->ho_ten,
                    'don_vi_quan_ly' => $donViQuanLy,
                    'ngay_muon' => $item->gio_muon,
                    'han_tra' => $item->gio_tra,
                    'so_ngay_tre' => $soNgayTre,
                    'tai_cho' => true,
                    'nguon' => 'doc_tai_cho',
                    'id_nguon' => $item->id_doc_tai_cho
                ];
            }

            return response()->json([
                'status' => 200,
                'message' => 'Lấy thống kê sách quá hạn thành công',
                'data' => $ketQua
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Lỗi khi lấy thống kê sách quá hạn',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function xuatExcelSachQuaHan(Request $request)
    {
        try {
            $validated = $this->validate($request, [
                'tu_ngay' => 'required|date',
                'den_ngay' => 'required|date|after_or_equal:tu_ngay',
            ], [
                'tu_ngay.required' => 'Vui lòng chọn ngày bắt đầu',
                'den_ngay.required' => 'Vui lòng chọn ngày kết thúc',
                'den_ngay.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu',
            ]);

            $tuNgay = Carbon::parse($validated['tu_ngay'])->startOfDay();
            $denNgay = Carbon::parse($validated['den_ngay'])->endOfDay();
            $ngayHienTai = Carbon::now();

            // Lấy sách mượn quá hạn
            $muonSachQuaHan = MuonSachModel::where('qua_han', 1)
                ->where(function($query) use ($tuNgay, $denNgay) {
                    $query->whereBetween('ngay_muon', [$tuNgay, $denNgay])
                        ->orWhereBetween('han_tra', [$tuNgay, $denNgay]);
                })
                ->get();

            // Lấy sách đọc tại chỗ quá hạn
            $docTaiChoQuaHan = DocTaiChoModel::where('qua_han', 1)
                ->where(function($query) use ($tuNgay, $denNgay) {
                    $query->whereBetween('gio_muon', [$tuNgay, $denNgay]);
                })
                ->get();

            $ketQua = [];

            // Xử lý dữ liệu sách mượn quá hạn
            foreach ($muonSachQuaHan as $item) {
                $dkcb = DKCBModel::find($item->id_dkcb);
                if (!$dkcb) continue;

                $sach = SachModel::find($dkcb->id_sach);
                if (!$sach) continue;

                $docGia = DocGiaModel::find($item->id_ban_doc);
                if (!$docGia) continue;

                $chuyenNganh = null;
                $donVi = null;

                if ($docGia->id_chuyen_nganh) {
                    $chuyenNganh = ChuyenNganhModel::find($docGia->id_chuyen_nganh);
                    if ($chuyenNganh && $chuyenNganh->id_don_vi) {
                        $donVi = DonViModel::find($chuyenNganh->id_don_vi);
                    }
                }

                $donViQuanLy = '';
                if ($donVi && $chuyenNganh) {
                    $donViQuanLy = $donVi->ten_don_vi . ' - ' . $chuyenNganh->ten_chuyen_nganh;
                } elseif ($chuyenNganh) {
                    $donViQuanLy = $chuyenNganh->ten_chuyen_nganh;
                }

                // Tính số ngày trễ cho sách mượn bằng cách lấy timestamp và tính chênh lệch ngày
                $hanTra = Carbon::parse($item->han_tra);
                $soNgayTre = 0;
                if ($ngayHienTai > $hanTra) {
                    // Chuyển về timestamps (seconds) rồi chuyển thành ngày
                    $chenhLechGiay = $ngayHienTai->timestamp - $hanTra->timestamp;
                    $soNgayTre = floor($chenhLechGiay / (60 * 60 * 24));
                }

                $ketQua[] = [
                    'id_dkcb' => $dkcb->id_dkcb,
                    'ma_dkcb' => $dkcb->ma_dkcb,
                    'nhan_de' => $sach->nhan_de,
                    'so_the' => $docGia->so_the,
                    'ho_ten' => $docGia->ho_ten,
                    'don_vi_quan_ly' => $donViQuanLy,
                    'ngay_muon' => $item->ngay_muon,
                    'han_tra' => $item->han_tra,
                    'so_ngay_tre' => $soNgayTre,
                    'tai_cho' => false,
                    'nguon' => 'muon_sach',
                    'id_nguon' => $item->id_muon_sach
                ];
            }

            // Xử lý dữ liệu sách đọc tại chỗ quá hạn
            foreach ($docTaiChoQuaHan as $item) {
                $dkcb = DKCBModel::find($item->id_dkcb);
                if (!$dkcb) continue;

                $sach = SachModel::find($dkcb->id_sach);
                if (!$sach) continue;

                $docGia = DocGiaModel::find($item->id_ban_doc);
                if (!$docGia) continue;

                $chuyenNganh = null;
                $donVi = null;

                if ($docGia->id_chuyen_nganh) {
                    $chuyenNganh = ChuyenNganhModel::find($docGia->id_chuyen_nganh);
                    if ($chuyenNganh && $chuyenNganh->id_don_vi) {
                        $donVi = DonViModel::find($chuyenNganh->id_don_vi);
                    }
                }

                $donViQuanLy = '';
                if ($donVi && $chuyenNganh) {
                    $donViQuanLy = $donVi->ten_don_vi . ' - ' . $chuyenNganh->ten_chuyen_nganh;
                } elseif ($chuyenNganh) {
                    $donViQuanLy = $chuyenNganh->ten_chuyen_nganh;
                }

                // Tính số ngày trễ cho đọc tại chỗ bằng cách lấy timestamp và tính chênh lệch ngày
                $gioMuon = Carbon::parse($item->gio_muon);
                $soNgayTre = 0;
                if ($ngayHienTai > $gioMuon) {
                    // Chuyển về timestamps (seconds) rồi chuyển thành ngày
                    $chenhLechGiay = $ngayHienTai->timestamp - $gioMuon->timestamp;
                    $soNgayTre = floor($chenhLechGiay / (60 * 60 * 24));
                }

                $ketQua[] = [
                    'id_dkcb' => $dkcb->id_dkcb,
                    'ma_dkcb' => $dkcb->ma_dkcb,
                    'nhan_de' => $sach->nhan_de,
                    'so_the' => $docGia->so_the,
                    'ho_ten' => $docGia->ho_ten,
                    'don_vi_quan_ly' => $donViQuanLy,
                    'ngay_muon' => $item->gio_muon,
                    'han_tra' => $item->gio_tra,
                    'so_ngay_tre' => $soNgayTre,
                    'tai_cho' => true,
                    'nguon' => 'doc_tai_cho',
                    'id_nguon' => $item->id_doc_tai_cho
                ];
            }

            // Xuất Excel dựa trên template
            $templatePath = public_path('template_excel/template_excel_bctk_sach_dang_muon_qua_han.xlsx');
            if (!file_exists($templatePath)) {
                return response()->json([
                    'status' => 500,
                    'message' => 'Không tìm thấy file mẫu báo cáo'
                ], 500);
            }

            // Tạo đối tượng PhpSpreadsheet từ file mẫu
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($templatePath);
            $sheet = $spreadsheet->getActiveSheet();

            // Thiết lập font chữ Times New Roman và kích thước 13 cho toàn bộ sheet
            $defaultFont = [
                'font' => [
                    'name' => 'Times New Roman',
                    'size' => 13,
                ]
            ];
            $sheet->getParent()->getDefaultStyle()->applyFromArray($defaultFont);

            // Cài đặt thông tin chung - Từ ngày đến ngày
            $tuNgayFormatted = Carbon::parse($validated['tu_ngay'])->format('d-m-Y');
            $denNgayFormatted = Carbon::parse($validated['den_ngay'])->format('d-m-Y');
            $sheet->setCellValue('A5', "Từ ngày: {$tuNgayFormatted} Đến ngày: {$denNgayFormatted}");

            // Chuẩn bị kiểu border
            $borderStyle = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ];

            // Điền dữ liệu sách 
            $startRow = 8;
            $stt = 1;
            $currentRow = $startRow;

            foreach ($ketQua as $item) {
                // Điền dữ liệu vào hàng hiện tại
                $sheet->setCellValue('A' . $currentRow, $stt);
                $sheet->setCellValue('B' . $currentRow, $item['ma_dkcb']);
                $sheet->setCellValue('C' . $currentRow, $item['nhan_de']);
                $sheet->setCellValue('D' . $currentRow, $item['so_the']);
                $sheet->setCellValue('E' . $currentRow, $item['ho_ten']);
                $sheet->setCellValue('F' . $currentRow, $item['don_vi_quan_ly']);
                $sheet->setCellValue('G' . $currentRow, Carbon::parse($item['ngay_muon'])->format('d/m/Y'));
                $sheet->setCellValue('H' . $currentRow, Carbon::parse($item['han_tra'])->format('d/m/Y'));
                $sheet->setCellValue('I' . $currentRow, value: $item['so_ngay_tre']);
                
                // Căn giữa cột STT và cột số ngày trễ
                $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('I' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Style cho hàng
                $sheet->getStyle('A' . $currentRow . ':I' . $currentRow)->applyFromArray($borderStyle);
                $sheet->getStyle('A' . $currentRow . ':I' . $currentRow)->applyFromArray($defaultFont);
                
                // Tăng số thứ tự và chuyển sang hàng tiếp theo
                $stt++;
                $currentRow++;
            }
            
            // Thêm dòng tổng cộng
            $tongSoSach = count($ketQua);
            $sheet->setCellValue('A' . $currentRow, 'TC');
            $sheet->setCellValue('B' . $currentRow, $tongSoSach);
            
            // Định dạng dòng tổng cộng
            $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
            $sheet->getStyle('B' . $currentRow)->getFont()->setBold(true);
            $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
            // Style cho hàng tổng cộng
            $sheet->getStyle('A' . $currentRow . ':I' . $currentRow)->applyFromArray($borderStyle);
            $sheet->getStyle('A' . $currentRow . ':I' . $currentRow)->applyFromArray($defaultFont);
            
            // Thêm thông tin người lập báo cáo ở cuối
            $currentRow += 2;
            $sheet->setCellValue('G' . $currentRow, "Vĩnh Long, ngày " . Carbon::now()->day . " tháng " . Carbon::now()->month . " năm " . Carbon::now()->year);
            $sheet->mergeCells('G' . $currentRow . ':I' . $currentRow);
            $sheet->getStyle('G' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('G' . $currentRow)->applyFromArray($defaultFont);
            
            $currentRow += 1;
            $sheet->setCellValue('G' . $currentRow, "Người lập báo cáo");
            $sheet->mergeCells('G' . $currentRow . ':I' . $currentRow);
            $sheet->getStyle('G' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('G' . $currentRow)->getFont()->setBold(true);
            $sheet->getStyle('G' . $currentRow)->applyFromArray($defaultFont);
            
            $currentRow += 4;
            $sheet->setCellValue('G' . $currentRow, session('HoTen') ?? '');
            $sheet->mergeCells('G' . $currentRow . ':I' . $currentRow);
            $sheet->getStyle('G' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('G' . $currentRow)->applyFromArray($defaultFont);
            
            // Tên file xuất 
            $fileName = 'BC_Sach_Qua_Han_' . Carbon::now()->format('dmY_His') . '.xlsx';
            
            // Trả về file Excel
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
            
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Lỗi xuất báo cáo sách quá hạn: ' . $e->getMessage());
            return response()->json([
                'status' => 500, 
                'message' => 'Đã xảy ra lỗi khi xuất báo cáo: ' . $e->getMessage()
            ], 500);
        }
    }

    public function thongKeBanDocDenThuVien(Request $request)
    {
        try {
            $request->validate([
                'tu_ngay' => 'required|date',
                'den_ngay' => 'required|date|after_or_equal:tu_ngay',
            ]);

            $tuNgay = Carbon::parse($request->tu_ngay)->startOfDay();
            $denNgay = Carbon::parse($request->den_ngay)->endOfDay();

            // Lấy dữ liệu từ bảng checkin_ban_doc
            $checkinBanDoc = CheckinBanDocModel::whereBetween('thoi_gian_den', [$tuNgay, $denNgay])
                ->get();

            $ketQua = [];

            foreach ($checkinBanDoc as $checkin) {
                $docGia = DocGiaModel::find($checkin->id_ban_doc);
                if (!$docGia) continue;

                $chuyenNganh = null;
                $donVi = null;

                if ($docGia->id_chuyen_nganh) {
                    $chuyenNganh = ChuyenNganhModel::find($docGia->id_chuyen_nganh);
                    if ($chuyenNganh && $chuyenNganh->id_don_vi) {
                        $donVi = DonViModel::find($chuyenNganh->id_don_vi);
                    }
                }

                $donViQuanLy = '';
                if ($donVi && $chuyenNganh) {
                    $donViQuanLy = $donVi->ten_don_vi . ' - ' . $chuyenNganh->ten_chuyen_nganh;
                } elseif ($chuyenNganh) {
                    $donViQuanLy = $chuyenNganh->ten_chuyen_nganh;
                }

                // Đếm số lượng sách mượn trong ngày
                $soLuongMuon = MuonSachModel::where('id_ban_doc', $checkin->id_ban_doc)
                    ->whereDate('ngay_muon', Carbon::parse($checkin->thoi_gian_den)->toDateString())
                    ->count();

                // Đếm số lượng sách đọc tại chỗ trong ngày
                $soLuongDocTaiCho = DocTaiChoModel::where('id_ban_doc', $checkin->id_ban_doc)
                    ->whereDate('gio_muon', Carbon::parse($checkin->thoi_gian_den)->toDateString())
                    ->count();

                // Đếm số lượng sách trả trong ngày
                $soLuongTra = LichSuMuonTraModel::where('id_ban_doc', $checkin->id_ban_doc)
                    ->whereDate('ngay_tra', Carbon::parse($checkin->thoi_gian_den)->toDateString())
                    ->count();

                $ketQua[] = [
                    'id_checkin' => $checkin->id_checkin_ban_doc,
                    'so_the' => $docGia->so_the,
                    'ho_ten' => $docGia->ho_ten,
                    'don_vi_quan_ly' => $donViQuanLy,
                    'thoi_gian_den' => $checkin->thoi_gian_den,
                    'so_luong_muon' => $soLuongMuon + $soLuongDocTaiCho,
                    'so_luong_tra' => $soLuongTra
                ];
            }

            return response()->json([
                'status' => 200,
                'message' => 'Lấy thống kê bạn đọc đến thư viện thành công',
                'data' => $ketQua
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Lỗi khi lấy thống kê bạn đọc đến thư viện',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function xuatExcelBanDocDenThuVien(Request $request)
    {
        try {
            $validated = $this->validate($request, [
                'tu_ngay' => 'required|date',
                'den_ngay' => 'required|date|after_or_equal:tu_ngay',
            ], [
                'tu_ngay.required' => 'Vui lòng chọn ngày bắt đầu',
                'den_ngay.required' => 'Vui lòng chọn ngày kết thúc',
                'den_ngay.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu',
            ]);

            $tuNgay = Carbon::parse($validated['tu_ngay'])->startOfDay();
            $denNgay = Carbon::parse($validated['den_ngay'])->endOfDay();

            // Lấy dữ liệu từ bảng checkin_ban_doc
            $checkinBanDoc = CheckinBanDocModel::whereBetween('thoi_gian_den', [$tuNgay, $denNgay])
                ->get();

            $ketQua = [];

            foreach ($checkinBanDoc as $checkin) {
                $docGia = DocGiaModel::find($checkin->id_ban_doc);
                if (!$docGia) continue;

                $chuyenNganh = null;
                $donVi = null;

                if ($docGia->id_chuyen_nganh) {
                    $chuyenNganh = ChuyenNganhModel::find($docGia->id_chuyen_nganh);
                    if ($chuyenNganh && $chuyenNganh->id_don_vi) {
                        $donVi = DonViModel::find($chuyenNganh->id_don_vi);
                    }
                }

                $donViQuanLy = '';
                if ($donVi && $chuyenNganh) {
                    $donViQuanLy = $donVi->ten_don_vi . ' - ' . $chuyenNganh->ten_chuyen_nganh;
                } elseif ($chuyenNganh) {
                    $donViQuanLy = $chuyenNganh->ten_chuyen_nganh;
                }

                // Đếm số lượng sách mượn trong ngày
                $soLuongMuon = MuonSachModel::where('id_ban_doc', $checkin->id_ban_doc)
                    ->whereDate('ngay_muon', Carbon::parse($checkin->thoi_gian_den)->toDateString())
                    ->count();

                // Đếm số lượng sách đọc tại chỗ trong ngày
                $soLuongDocTaiCho = DocTaiChoModel::where('id_ban_doc', $checkin->id_ban_doc)
                    ->whereDate('gio_muon', Carbon::parse($checkin->thoi_gian_den)->toDateString())
                    ->count();

                // Đếm số lượng sách trả trong ngày
                $soLuongTra = LichSuMuonTraModel::where('id_ban_doc', $checkin->id_ban_doc)
                    ->whereDate('ngay_tra', Carbon::parse($checkin->thoi_gian_den)->toDateString())
                    ->count();

                $ketQua[] = [
                    'id_checkin' => $checkin->id_checkin_ban_doc,
                    'so_the' => $docGia->so_the,
                    'ho_ten' => $docGia->ho_ten,
                    'don_vi_quan_ly' => $donViQuanLy,
                    'thoi_gian_den' => $checkin->thoi_gian_den,
                    'so_luong_muon' => $soLuongMuon + $soLuongDocTaiCho,
                    'so_luong_tra' => $soLuongTra
                ];
            }

            // Xuất Excel dựa trên template
            $templatePath = public_path('template_excel/template_excel_bctk_ban_doc_den_thu_vien.xlsx');
            if (!file_exists($templatePath)) {
                return response()->json([
                    'status' => 500,
                    'message' => 'Không tìm thấy file mẫu báo cáo'
                ], 500);
            }

            // Tạo đối tượng PhpSpreadsheet từ file mẫu
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($templatePath);
            $sheet = $spreadsheet->getActiveSheet();

            // Thiết lập font chữ Times New Roman và kích thước 13 cho toàn bộ sheet
            $defaultFont = [
                'font' => [
                    'name' => 'Times New Roman',
                    'size' => 13,
                ]
            ];
            $sheet->getParent()->getDefaultStyle()->applyFromArray($defaultFont);

            // Cài đặt thông tin chung - Từ ngày đến ngày
            $tuNgayFormatted = Carbon::parse($validated['tu_ngay'])->format('d-m-Y');
            $denNgayFormatted = Carbon::parse($validated['den_ngay'])->format('d-m-Y');
            $sheet->setCellValue('A5', "Từ ngày: {$tuNgayFormatted} Đến ngày: {$denNgayFormatted}");

            // Chuẩn bị kiểu border
            $borderStyle = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ];

            // Điền dữ liệu bạn đọc
            $startRow = 8;
            $stt = 1;
            $currentRow = $startRow;

            foreach ($ketQua as $item) {
                // Điền dữ liệu vào hàng hiện tại
                $sheet->setCellValue('A' . $currentRow, $stt);
                $sheet->setCellValue('B' . $currentRow, $item['so_the']);
                $sheet->setCellValue('C' . $currentRow, $item['ho_ten']);
                $sheet->setCellValue('D' . $currentRow, $item['don_vi_quan_ly']);
                $sheet->setCellValue('E' . $currentRow, Carbon::parse($item['thoi_gian_den'])->format('d/m/Y'));
                $sheet->setCellValue('F' . $currentRow, $item['so_luong_muon']);
                $sheet->setCellValue('G' . $currentRow, $item['so_luong_tra']);
                
                // Căn giữa các cột số
                $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('F' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('G' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Style cho hàng
                $sheet->getStyle('A' . $currentRow . ':G' . $currentRow)->applyFromArray($borderStyle);
                $sheet->getStyle('A' . $currentRow . ':G' . $currentRow)->applyFromArray($defaultFont);
                
                // Tăng số thứ tự và chuyển sang hàng tiếp theo
                $stt++;
                $currentRow++;
            }
            
            // Thêm dòng tổng cộng
            $tongSoBanDoc = count($ketQua);
            $sheet->setCellValue('A' . $currentRow, 'TC');
            $sheet->setCellValue('B' . $currentRow, $tongSoBanDoc);
            
            // Định dạng dòng tổng cộng
            $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
            $sheet->getStyle('B' . $currentRow)->getFont()->setBold(true);
            $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
            // Style cho hàng tổng cộng
            $sheet->getStyle('A' . $currentRow . ':G' . $currentRow)->applyFromArray($borderStyle);
            $sheet->getStyle('A' . $currentRow . ':G' . $currentRow)->applyFromArray($defaultFont);
            
            // Thêm thông tin người lập báo cáo ở cuối
            $currentRow += 2;
            $sheet->setCellValue('E' . $currentRow, "Vĩnh Long, ngày " . Carbon::now()->day . " tháng " . Carbon::now()->month . " năm " . Carbon::now()->year);
            $sheet->mergeCells('E' . $currentRow . ':G' . $currentRow);
            $sheet->getStyle('E' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E' . $currentRow)->applyFromArray($defaultFont);
            
            $currentRow += 1;
            $sheet->setCellValue('E' . $currentRow, "Người lập báo cáo");
            $sheet->mergeCells('E' . $currentRow . ':G' . $currentRow);
            $sheet->getStyle('E' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E' . $currentRow)->getFont()->setBold(true);
            $sheet->getStyle('E' . $currentRow)->applyFromArray($defaultFont);
            
            $currentRow += 4;
            $sheet->setCellValue('E' . $currentRow, session('HoTen') ?? '');
            $sheet->mergeCells('E' . $currentRow . ':G' . $currentRow);
            $sheet->getStyle('E' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E' . $currentRow)->applyFromArray($defaultFont);
            
            // Tên file xuất 
            $fileName = 'BC_Ban_Doc_Den_Thu_Vien_' . Carbon::now()->format('dmY_His') . '.xlsx';
            
            // Trả về file Excel
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
            
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Lỗi xuất báo cáo bạn đọc đến thư viện: ' . $e->getMessage());
            return response()->json([
                'status' => 500, 
                'message' => 'Đã xảy ra lỗi khi xuất báo cáo: ' . $e->getMessage()
            ], 500);
        }
    }

    public function thongKeTinhHinhPhucVuBanDoc(Request $request)
    {
        try {
            $request->validate([
                'tu_ngay' => 'required|date',
                'den_ngay' => 'required|date|after_or_equal:tu_ngay',
            ]);

            $tuNgay = Carbon::parse($request->tu_ngay)->startOfDay();
            $denNgay = Carbon::parse($request->den_ngay)->endOfDay();

            // Tạo mảng các ngày trong khoảng thời gian
            $danhSachNgay = [];
            $currentDate = clone $tuNgay;
            while ($currentDate <= $denNgay) {
                $danhSachNgay[] = $currentDate->format('Y-m-d');
                $currentDate->addDay();
            }

            // Lấy danh sách đối tượng bạn đọc
            $doiTuongSV = DoiTuongBanDocModel::where('ma_so_quy_uoc', '!=', '99')->pluck('id_doi_tuong_ban_doc')->toArray();
            $doiTuongCBGV = DoiTuongBanDocModel::where('ma_so_quy_uoc', '99')->pluck('id_doi_tuong_ban_doc')->toArray();

            // Lấy danh sách bạn đọc là SV và CB-GV
            $banDocSV = DocGiaModel::whereIn('ma_so_quy_uoc', function ($query) use ($doiTuongSV) {
                $query->select('ma_so_quy_uoc')
                    ->from('doi_tuong_ban_doc')
                    ->whereIn('id_doi_tuong_ban_doc', $doiTuongSV);
            })->pluck('id_doc_gia')->toArray();

            $banDocCBGV = DocGiaModel::whereIn('ma_so_quy_uoc', function ($query) {
                $query->select('ma_so_quy_uoc')
                    ->from('doi_tuong_ban_doc')
                    ->where('ma_so_quy_uoc', '99');
            })->pluck('id_doc_gia')->toArray();

            $ketQua = [];

            // Xử lý dữ liệu theo từng ngày
            foreach ($danhSachNgay as $ngay) {
                $startOfDay = Carbon::parse($ngay)->startOfDay();
                $endOfDay = Carbon::parse($ngay)->endOfDay();

                // 1. Đọc giả đến thư viện
                $dgDenThuVienSV = CheckinBanDocModel::whereBetween('thoi_gian_den', [$startOfDay, $endOfDay])
                    ->whereIn('id_ban_doc', $banDocSV)
                    ->distinct('id_ban_doc')
                    ->count('id_ban_doc');

                $dgDenThuVienCBGV = CheckinBanDocModel::whereBetween('thoi_gian_den', [$startOfDay, $endOfDay])
                    ->whereIn('id_ban_doc', $banDocCBGV)
                    ->distinct('id_ban_doc')
                    ->count('id_ban_doc');

                // 2. Đọc giả mượn sách
                // Mượn về nhà
                $dgMuonSachSV = MuonSachModel::whereBetween('ngay_muon', [$startOfDay, $endOfDay])
                    ->whereIn('id_ban_doc', $banDocSV)
                    ->distinct('id_ban_doc')
                    ->count('id_ban_doc');

                $dgMuonSachCBGV = MuonSachModel::whereBetween('ngay_muon', [$startOfDay, $endOfDay])
                    ->whereIn('id_ban_doc', $banDocCBGV)
                    ->distinct('id_ban_doc')
                    ->count('id_ban_doc');

                // Đọc tại chỗ
                $dgDocTaiChoSV = DocTaiChoModel::whereBetween('gio_muon', [$startOfDay, $endOfDay])
                    ->whereIn('id_ban_doc', $banDocSV)
                    ->distinct('id_ban_doc')
                    ->count('id_ban_doc');

                $dgDocTaiChoCBGV = DocTaiChoModel::whereBetween('gio_muon', [$startOfDay, $endOfDay])
                    ->whereIn('id_ban_doc', $banDocCBGV)
                    ->distinct('id_ban_doc')
                    ->count('id_ban_doc');

                // Tổng số đọc giả mượn sách (cả mượn về nhà và đọc tại chỗ)
                $tongDGMuonSachSV = DB::table(function ($query) use ($startOfDay, $endOfDay, $banDocSV) {
                    $query->select('id_ban_doc')
                        ->from('muon_sach')
                        ->whereBetween('ngay_muon', [$startOfDay, $endOfDay])
                        ->whereIn('id_ban_doc', $banDocSV)
                        ->union(
                            DB::table('doc_tai_cho')
                                ->select('id_ban_doc')
                                ->whereBetween('gio_muon', [$startOfDay, $endOfDay])
                                ->whereIn('id_ban_doc', $banDocSV)
                        );
                }, 'combined')->distinct()->count();

                $tongDGMuonSachCBGV = DB::table(function ($query) use ($startOfDay, $endOfDay, $banDocCBGV) {
                    $query->select('id_ban_doc')
                        ->from('muon_sach')
                        ->whereBetween('ngay_muon', [$startOfDay, $endOfDay])
                        ->whereIn('id_ban_doc', $banDocCBGV)
                        ->union(
                            DB::table('doc_tai_cho')
                                ->select('id_ban_doc')
                                ->whereBetween('gio_muon', [$startOfDay, $endOfDay])
                                ->whereIn('id_ban_doc', $banDocCBGV)
                        );
                }, 'combined')->distinct()->count();

                // 3. Sách mượn
                // Đọc tại chỗ
                $sachDocTaiChoSV = DocTaiChoModel::whereBetween('gio_muon', [$startOfDay, $endOfDay])
                    ->whereIn('id_ban_doc', $banDocSV)
                    ->count();

                $sachDocTaiChoCBGV = DocTaiChoModel::whereBetween('gio_muon', [$startOfDay, $endOfDay])
                    ->whereIn('id_ban_doc', $banDocCBGV)
                    ->count();

                // Mượn về nhà
                $sachMuonVeNhaSV = MuonSachModel::whereBetween('ngay_muon', [$startOfDay, $endOfDay])
                    ->whereIn('id_ban_doc', $banDocSV)
                    ->count();

                $sachMuonVeNhaCBGV = MuonSachModel::whereBetween('ngay_muon', [$startOfDay, $endOfDay])
                    ->whereIn('id_ban_doc', $banDocCBGV)
                    ->count();

                // 4. Sách trả
                $sachTraSV = LichSuMuonTraModel::whereBetween('ngay_tra', [$startOfDay, $endOfDay])
                    ->whereIn('id_ban_doc', $banDocSV)
                    ->count();

                $sachTraCBGV = LichSuMuonTraModel::whereBetween('ngay_tra', [$startOfDay, $endOfDay])
                    ->whereIn('id_ban_doc', $banDocCBGV)
                    ->count();

                // 5. Sách quá hạn
                $sachQuaHanSV = MuonSachModel::where('qua_han', 1)
                    ->whereIn('id_ban_doc', $banDocSV)
                    ->count() + 
                    DocTaiChoModel::where('qua_han', 1)
                    ->whereIn('id_ban_doc', $banDocSV)
                    ->count();

                $sachQuaHanCBGV = MuonSachModel::where('qua_han', 1)
                    ->whereIn('id_ban_doc', $banDocCBGV)
                    ->count() + 
                    DocTaiChoModel::where('qua_han', 1)
                    ->whereIn('id_ban_doc', $banDocCBGV)
                    ->count();

                // 6. Xử lý vi phạm
                $xuLyViPhamSV = XuLyViPhamModel::whereBetween('ngay_phat', [$startOfDay, $endOfDay])
                    ->whereIn('id_ban_doc', $banDocSV)
                    ->count();

                $xuLyViPhamCBGV = XuLyViPhamModel::whereBetween('ngay_phat', [$startOfDay, $endOfDay])
                    ->whereIn('id_ban_doc', $banDocCBGV)
                    ->count();

                // 7. Số lần cấp thẻ phân theo từng lần (1, 2, 3)
                // Lần 1
                $soLanCapThe1SV = DocGiaModel::whereIn('id_doc_gia', function ($query) use ($startOfDay, $endOfDay) {
                    $query->select('id_ban_doc')
                        ->from('checkin_ban_doc')
                        ->whereBetween('thoi_gian_den', [$startOfDay, $endOfDay]);
                })
                ->whereIn('id_doc_gia', $banDocSV)
                ->where('lan_cap_the', 1)
                ->count();

                $soLanCapThe1CBGV = DocGiaModel::whereIn('id_doc_gia', function ($query) use ($startOfDay, $endOfDay) {
                    $query->select('id_ban_doc')
                        ->from('checkin_ban_doc')
                        ->whereBetween('thoi_gian_den', [$startOfDay, $endOfDay]);
                })
                ->whereIn('id_doc_gia', $banDocCBGV)
                ->where('lan_cap_the', 1)
                ->count();

                // Lần 2
                $soLanCapThe2SV = DocGiaModel::whereIn('id_doc_gia', function ($query) use ($startOfDay, $endOfDay) {
                    $query->select('id_ban_doc')
                        ->from('checkin_ban_doc')
                        ->whereBetween('thoi_gian_den', [$startOfDay, $endOfDay]);
                })
                ->whereIn('id_doc_gia', $banDocSV)
                ->where('lan_cap_the', 2)
                ->count();

                $soLanCapThe2CBGV = DocGiaModel::whereIn('id_doc_gia', function ($query) use ($startOfDay, $endOfDay) {
                    $query->select('id_ban_doc')
                        ->from('checkin_ban_doc')
                        ->whereBetween('thoi_gian_den', [$startOfDay, $endOfDay]);
                })
                ->whereIn('id_doc_gia', $banDocCBGV)
                ->where('lan_cap_the', 2)
                ->count();

                // Lần 3 trở lên
                $soLanCapThe3SV = DocGiaModel::whereIn('id_doc_gia', function ($query) use ($startOfDay, $endOfDay) {
                    $query->select('id_ban_doc')
                        ->from('checkin_ban_doc')
                        ->whereBetween('thoi_gian_den', [$startOfDay, $endOfDay]);
                })
                ->whereIn('id_doc_gia', $banDocSV)
                ->where('lan_cap_the', '>=', 3)
                ->count();

                $soLanCapThe3CBGV = DocGiaModel::whereIn('id_doc_gia', function ($query) use ($startOfDay, $endOfDay) {
                    $query->select('id_ban_doc')
                        ->from('checkin_ban_doc')
                        ->whereBetween('thoi_gian_den', [$startOfDay, $endOfDay]);
                })
                ->whereIn('id_doc_gia', $banDocCBGV)
                ->where('lan_cap_the', '>=', 3)
                ->count();

                // Thêm dữ liệu của ngày vào kết quả
                $ketQua[] = [
                    'ngay' => Carbon::parse($ngay)->format('d-m-Y'),
                    'dg_den_tv_sv' => $dgDenThuVienSV,
                    'dg_den_tv_cbgv' => $dgDenThuVienCBGV,
                    'dg_muon_sach_sv' => $tongDGMuonSachSV,
                    'dg_muon_sach_cbgv' => $tongDGMuonSachCBGV,
                    'sach_doc_tai_cho_sv' => $sachDocTaiChoSV,
                    'sach_doc_tai_cho_cbgv' => $sachDocTaiChoCBGV,
                    'sach_muon_ve_nha_sv' => $sachMuonVeNhaSV,
                    'sach_muon_ve_nha_cbgv' => $sachMuonVeNhaCBGV,
                    'sach_tra_sv' => $sachTraSV,
                    'sach_tra_cbgv' => $sachTraCBGV,
                    'sach_qua_han_sv' => $sachQuaHanSV,
                    'sach_qua_han_cbgv' => $sachQuaHanCBGV,
                    'xu_ly_vi_pham_sv' => $xuLyViPhamSV,
                    'xu_ly_vi_pham_cbgv' => $xuLyViPhamCBGV,
                    'cap_the_1_sv' => $soLanCapThe1SV,
                    'cap_the_1_cbgv' => $soLanCapThe1CBGV,
                    'cap_the_2_sv' => $soLanCapThe2SV,
                    'cap_the_2_cbgv' => $soLanCapThe2CBGV,
                    'cap_the_3_sv' => $soLanCapThe3SV,
                    'cap_the_3_cbgv' => $soLanCapThe3CBGV
                ];
            }

            // Tính tổng cộng cho tất cả các ngày
            $tongCong = [
                'dg_den_tv_sv' => 0,
                'dg_den_tv_cbgv' => 0,
                'dg_muon_sach_sv' => 0,
                'dg_muon_sach_cbgv' => 0,
                'sach_doc_tai_cho_sv' => 0,
                'sach_doc_tai_cho_cbgv' => 0,
                'sach_muon_ve_nha_sv' => 0,
                'sach_muon_ve_nha_cbgv' => 0,
                'sach_tra_sv' => 0,
                'sach_tra_cbgv' => 0,
                'sach_qua_han_sv' => 0,
                'sach_qua_han_cbgv' => 0,
                'xu_ly_vi_pham_sv' => 0,
                'xu_ly_vi_pham_cbgv' => 0,
                'cap_the_1_sv' => 0,
                'cap_the_1_cbgv' => 0,
                'cap_the_2_sv' => 0,
                'cap_the_2_cbgv' => 0,
                'cap_the_3_sv' => 0,
                'cap_the_3_cbgv' => 0
            ];

            foreach ($ketQua as $item) {
                foreach ($tongCong as $key => $value) {
                    $tongCong[$key] += $item[$key];
                }
            }

            return response()->json([
                'status' => 200,
                'message' => 'Lấy dữ liệu thành công',
                'data' => $ketQua,
                'tong_cong' => $tongCong
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi thống kê',
                'error' => $e->getMessage()
            ]);
        }
    }
    
    public function xuatExcelTinhHinhPhucVuBanDoc(Request $request)
    {
        try {
            $request->validate([
                'tu_ngay' => 'required|date',
                'den_ngay' => 'required|date|after_or_equal:tu_ngay',
            ]);
            
            $tuNgay = Carbon::parse($request->tu_ngay)->startOfDay();
            $denNgay = Carbon::parse($request->den_ngay)->endOfDay();
            
            // Lấy thông tin báo cáo
            $response = $this->thongKeTinhHinhPhucVuBanDoc($request);
            $data = json_decode($response->getContent(), true);
            
            if ($data['status'] !== 200) {
                return back()->with('error', $data['message']);
            }
            
            $ketQua = $data['data'];
            $tongCong = $data['tong_cong'];
            
            // Xuất Excel dựa trên template
            $templatePath = public_path('template_excel/template_excel_bctk_tinh_hinh_phuc_vu_ban_doc.xlsx');
            if (!file_exists($templatePath)) {
                return response()->json([
                    'status' => 500,
                    'message' => 'Không tìm thấy file mẫu báo cáo'
                ], 500);
            }

            // Tạo đối tượng PhpSpreadsheet từ file mẫu
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($templatePath);
            $sheet = $spreadsheet->getActiveSheet();
            
            // Thiết lập font chữ Times New Roman và kích thước 13 cho toàn bộ sheet
            $defaultFont = [
                'font' => [
                    'name' => 'Times New Roman',
                    'size' => 13,
                ]
            ];
            $sheet->getParent()->getDefaultStyle()->applyFromArray($defaultFont);
            
            // Chuẩn bị kiểu border
            $borderStyle = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ];
            
            // Cài đặt thông tin chung - Từ ngày đến ngày
            $tuNgayFormatted = Carbon::parse($request->tu_ngay)->format('d-m-Y');
            $denNgayFormatted = Carbon::parse($request->den_ngay)->format('d-m-Y');
            $sheet->setCellValue('A5', "Từ ngày: {$tuNgayFormatted} Đến ngày: {$denNgayFormatted}");
            
            // Đổ dữ liệu vào bảng - bắt đầu từ dòng 10 theo yêu cầu
            $startRow = 10;
            $row = $startRow;
            foreach ($ketQua as $index => $item) {
                $sheet->setCellValue('A' . $row, $index + 1);
                $sheet->setCellValue('B' . $row, $item['ngay']);
                $sheet->setCellValue('C' . $row, $item['dg_den_tv_sv']);
                $sheet->setCellValue('D' . $row, $item['dg_den_tv_cbgv']);
                $sheet->setCellValue('E' . $row, $item['dg_muon_sach_sv']);
                $sheet->setCellValue('F' . $row, $item['dg_muon_sach_cbgv']);
                $sheet->setCellValue('G' . $row, $item['sach_doc_tai_cho_sv']);
                $sheet->setCellValue('H' . $row, $item['sach_doc_tai_cho_cbgv']);
                $sheet->setCellValue('I' . $row, $item['sach_muon_ve_nha_sv']);
                $sheet->setCellValue('J' . $row, $item['sach_muon_ve_nha_cbgv']);
                $sheet->setCellValue('K' . $row, $item['sach_tra_sv']);
                $sheet->setCellValue('L' . $row, $item['sach_tra_cbgv']);
                $sheet->setCellValue('M' . $row, $item['sach_qua_han_sv']);
                $sheet->setCellValue('N' . $row, $item['sach_qua_han_cbgv']);
                $sheet->setCellValue('O' . $row, $item['xu_ly_vi_pham_sv']);
                $sheet->setCellValue('P' . $row, $item['xu_ly_vi_pham_cbgv']);
                $sheet->setCellValue('Q' . $row, $item['cap_the_1_sv']);
                $sheet->setCellValue('R' . $row, $item['cap_the_1_cbgv']);
                $sheet->setCellValue('S' . $row, $item['cap_the_2_sv']);
                $sheet->setCellValue('T' . $row, $item['cap_the_2_cbgv']);
                $sheet->setCellValue('U' . $row, $item['cap_the_3_sv']);
                $sheet->setCellValue('V' . $row, $item['cap_the_3_cbgv']);
                
                // Định dạng dữ liệu
                $sheet->getStyle('A' . $row . ':V' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                
                // Thêm border cho mỗi dòng dữ liệu
                $sheet->getStyle('A' . $row . ':V' . $row)->applyFromArray($borderStyle);
                
                $row++;
            }
            
            // Thêm dòng tổng cộng
            $sheet->setCellValue('A' . $row, 'TC');
            $sheet->setCellValue('B' . $row, count($ketQua));
            $sheet->setCellValue('C' . $row, $tongCong['dg_den_tv_sv']);
            $sheet->setCellValue('D' . $row, $tongCong['dg_den_tv_cbgv']);
            $sheet->setCellValue('E' . $row, $tongCong['dg_muon_sach_sv']);
            $sheet->setCellValue('F' . $row, $tongCong['dg_muon_sach_cbgv']);
            $sheet->setCellValue('G' . $row, $tongCong['sach_doc_tai_cho_sv']);
            $sheet->setCellValue('H' . $row, $tongCong['sach_doc_tai_cho_cbgv']);
            $sheet->setCellValue('I' . $row, $tongCong['sach_muon_ve_nha_sv']);
            $sheet->setCellValue('J' . $row, $tongCong['sach_muon_ve_nha_cbgv']);
            $sheet->setCellValue('K' . $row, $tongCong['sach_tra_sv']);
            $sheet->setCellValue('L' . $row, $tongCong['sach_tra_cbgv']);
            $sheet->setCellValue('M' . $row, $tongCong['sach_qua_han_sv']);
            $sheet->setCellValue('N' . $row, $tongCong['sach_qua_han_cbgv']);
            $sheet->setCellValue('O' . $row, $tongCong['xu_ly_vi_pham_sv']);
            $sheet->setCellValue('P' . $row, $tongCong['xu_ly_vi_pham_cbgv']);
            $sheet->setCellValue('Q' . $row, $tongCong['cap_the_1_sv']);
            $sheet->setCellValue('R' . $row, $tongCong['cap_the_1_cbgv']);
            $sheet->setCellValue('S' . $row, $tongCong['cap_the_2_sv']);
            $sheet->setCellValue('T' . $row, $tongCong['cap_the_2_cbgv']);
            $sheet->setCellValue('U' . $row, $tongCong['cap_the_3_sv']);
            $sheet->setCellValue('V' . $row, $tongCong['cap_the_3_cbgv']);
            
            // Định dạng dòng tổng cộng
            $sheet->getStyle('A' . $row . ':V' . $row)->getFont()->setBold(true);
            $sheet->getStyle('A' . $row . ':V' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            // Thêm border cho dòng tổng cộng
            $sheet->getStyle('A' . $row . ':V' . $row)->applyFromArray($borderStyle);
            
            // Thêm dòng tổng số
            $row++;
            $sheet->setCellValue('A' . $row, 'Tổng cộng');
            $sheet->mergeCells('A' . $row . ':B' . $row);
            $sheet->setCellValue('C' . $row, $tongCong['dg_den_tv_sv'] + $tongCong['dg_den_tv_cbgv']);
            $sheet->mergeCells('C' . $row . ':D' . $row);
            $sheet->setCellValue('E' . $row, $tongCong['dg_muon_sach_sv'] + $tongCong['dg_muon_sach_cbgv']);
            $sheet->mergeCells('E' . $row . ':F' . $row);
            $sheet->setCellValue('G' . $row, $tongCong['sach_doc_tai_cho_sv'] + $tongCong['sach_doc_tai_cho_cbgv']);
            $sheet->mergeCells('G' . $row . ':H' . $row);
            $sheet->setCellValue('I' . $row, $tongCong['sach_muon_ve_nha_sv'] + $tongCong['sach_muon_ve_nha_cbgv']);
            $sheet->mergeCells('I' . $row . ':J' . $row);
            $sheet->setCellValue('K' . $row, $tongCong['sach_tra_sv'] + $tongCong['sach_tra_cbgv']);
            $sheet->mergeCells('K' . $row . ':L' . $row);
            $sheet->setCellValue('M' . $row, $tongCong['sach_qua_han_sv'] + $tongCong['sach_qua_han_cbgv']);
            $sheet->mergeCells('M' . $row . ':N' . $row);
            $sheet->setCellValue('O' . $row, $tongCong['xu_ly_vi_pham_sv'] + $tongCong['xu_ly_vi_pham_cbgv']);
            $sheet->mergeCells('O' . $row . ':P' . $row);
            $sheet->setCellValue('Q' . $row, $tongCong['cap_the_1_sv'] + $tongCong['cap_the_1_cbgv']);
            $sheet->mergeCells('Q' . $row . ':R' . $row);
            $sheet->setCellValue('S' . $row, $tongCong['cap_the_2_sv'] + $tongCong['cap_the_2_cbgv']);
            $sheet->mergeCells('S' . $row . ':T' . $row);
            $sheet->setCellValue('U' . $row, $tongCong['cap_the_3_sv'] + $tongCong['cap_the_3_cbgv']);
            $sheet->mergeCells('U' . $row . ':V' . $row);
            
            // Định dạng dòng tổng số
            $sheet->getStyle('A' . $row . ':V' . $row)->getFont()->setBold(true);
            $sheet->getStyle('A' . $row . ':V' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            // Thêm border cho dòng tổng số
            $sheet->getStyle('A' . $row . ':V' . $row)->applyFromArray($borderStyle);
            
            // Thêm thông tin người lập báo cáo ở cuối
            $currentRow = $row + 3;
            $sheet->setCellValue('Q' . $currentRow, "Vĩnh Long, ngày " . Carbon::now()->day . " tháng " . Carbon::now()->month . " năm " . Carbon::now()->year);
            $sheet->mergeCells('Q' . $currentRow . ':V' . $currentRow);
            $sheet->getStyle('Q' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
            $currentRow += 1;
            $sheet->setCellValue('Q' . $currentRow, "Người lập báo cáo");
            $sheet->mergeCells('Q' . $currentRow . ':V' . $currentRow);
            $sheet->getStyle('Q' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('Q' . $currentRow)->getFont()->setBold(true);
            
            $currentRow += 4;
            $sheet->setCellValue('Q' . $currentRow, session('HoTen') ?? '');
            $sheet->mergeCells('Q' . $currentRow . ':V' . $currentRow);
            $sheet->getStyle('Q' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
            // Tên file xuất 
            $fileName = 'BC_Tinh_Hinh_Phuc_Vu_Ban_Doc_' . Carbon::now()->format('dmY_His') . '.xlsx';
            
            // Trả về file Excel
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
            
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Lỗi xuất báo cáo tình hình phục vụ bạn đọc: ' . $e->getMessage());
            return response()->json([
                'status' => 500, 
                'message' => 'Đã xảy ra lỗi khi xuất báo cáo: ' . $e->getMessage()
            ], 500);
        }
    }
}
