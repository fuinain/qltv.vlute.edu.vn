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
use Illuminate\Http\Request;
use Carbon\Carbon;

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
}
