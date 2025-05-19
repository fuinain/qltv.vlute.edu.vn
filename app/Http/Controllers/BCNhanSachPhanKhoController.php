<?php

namespace App\Http\Controllers;

use App\Models\DonNhanModel;
use App\Models\KhoAnPhamModel;
use App\Models\SachModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class BCNhanSachPhanKhoController extends Controller
{
    /**
     * Xuất báo cáo nhận sách phân kho dưới dạng file Excel
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        try {
            $validated = $this->validate($request, [
                'don_nhan_bat_dau' => 'required|integer|min:1',
                'don_nhan_ket_thuc' => 'required|integer|min:1',
                'danhSachSach' => 'nullable|string',
            ], [
                'don_nhan_bat_dau.required' => 'Vui lòng nhập đơn nhận bắt đầu',
                'don_nhan_bat_dau.integer' => 'Đơn nhận bắt đầu phải là số nguyên',
                'don_nhan_ket_thuc.required' => 'Vui lòng nhập đơn nhận kết thúc',
                'don_nhan_ket_thuc.integer' => 'Đơn nhận kết thúc phải là số nguyên',
            ]);

            $donNhanBatDau = $validated['don_nhan_bat_dau'];
            $donNhanKetThuc = $validated['don_nhan_ket_thuc'];
            
            // Danh sách các đơn nhận cần xuất báo cáo
            $donNhanIds = range($donNhanBatDau, $donNhanKetThuc);
            
            // Lấy danh sách sách từ JSON string (nếu có)
            $danhSachSach = [];
            if (!empty($validated['danhSachSach'])) {
                $danhSachSach = json_decode($validated['danhSachSach'], true);
            } else {
                // Nếu không có danh sách sách từ FE, lấy từ database
                $cacDonNhanTonTai = DonNhanModel::whereIn('id_don_nhan', $donNhanIds)->pluck('id_don_nhan')->toArray();
                
                if (empty($cacDonNhanTonTai)) {
                    return response()->json([
                        'status' => 404,
                        'message' => 'Không tìm thấy đơn nhận nào trong khoảng này'
                    ], 404);
                }
                
                // Lấy danh sách sách từ database
                $sachList = SachModel::whereIn('id_don_nhan', $cacDonNhanTonTai)
                    ->with(['bienMucBieuGhi.truongCha.children'])
                    ->get();
                
                // Xử lý dữ liệu
                foreach ($sachList as $sach) {
                    $phanLoai = ['', ''];
                    $dkcbList = [];
                    
                    // Lấy DKCB của sách
                    $danhSachDKCB = DB::table('dkcb')
                        ->where('id_sach', $sach->id_sach)
                        ->where('trang_thai', 1)
                        ->get();
                    
                    foreach ($danhSachDKCB as $dkcb) {
                        $dkcbList[] = $dkcb->ma_dkcb;
                    }
                    
                    // Tìm trường phân loại 082
                    if ($sach->bienMucBieuGhi) {
                        $truong082 = $sach->bienMucBieuGhi->truongCha
                            ->where('ma_truong', '082')
                            ->first();
                        
                        if ($truong082) {
                            // Tìm các trường con a và b
                            $con_a = $truong082->children->where('ma_truong_con', 'a')->first();
                            $con_b = $truong082->children->where('ma_truong_con', 'b')->first();
                            
                            if ($con_a && !empty($con_a->noi_dung)) {
                                $phanLoai[0] = $con_a->noi_dung;
                            }
                            if ($con_b && !empty($con_b->noi_dung)) {
                                $phanLoai[1] = $con_b->noi_dung;
                            }
                        }
                    }
                    
                    // Thêm vào danh sách sách
                    $danhSachSach[] = [
                        'id_don_nhan' => $sach->id_don_nhan,
                        'id_sach' => $sach->id_sach,
                        'nhan_de' => $sach->nhan_de,
                        'tac_gia' => $sach->tac_gia,
                        'nam_xuat_ban' => $sach->nam_xuat_ban,
                        'nha_xuat_ban' => $sach->nha_xuat_ban,
                        'noi_xuat_ban' => $sach->noi_xuat_ban,
                        'phan_loai_1' => $phanLoai[0],
                        'phan_loai_2' => $phanLoai[1],
                        'gia' => $sach->gia,
                        'so_luong' => $sach->so_luong,
                        'thanh_tien' => $sach->thanh_tien,
                        'dkcb_list' => $dkcbList
                    ];
                }
            }

            // Xuất Excel dựa trên template
            $templatePath = public_path('template_excel/template_excel_bcpk.xlsx');
            if (!file_exists($templatePath)) {
                return response()->json([
                    'status' => 500,
                    'message' => 'Không tìm thấy file mẫu báo cáo'
                ], 500);
            }

            $spreadsheet = IOFactory::load($templatePath);
            $sheet = $spreadsheet->getActiveSheet();

            // Thiết lập font chữ Times New Roman và kích thước 13 cho toàn bộ sheet
            $defaultFont = [
                'font' => [
                    'name' => 'Times New Roman',
                    'size' => 13,
                ]
            ];
            $sheet->getParent()->getDefaultStyle()->applyFromArray($defaultFont);

            // Cài đặt thông tin chung
            $sheet->setCellValue('A4', "DANH MỤC SÁCH ĐƠN NHẬN TỪ SỐ: {$donNhanBatDau} ĐẾN SỐ: {$donNhanKetThuc}");

            // Chuẩn bị kiểu border
            $borderStyle = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ];

            // Điền dữ liệu sách 
            $startRow = 6;
            $stt = 1;
            $currentRow = $startRow;
            
            // Tính tổng
            $tongSoLuong = 0;
            $tongDKCB = 0;
            $tongThanhTien = 0;

            foreach ($danhSachSach as $sach) {
                // Tính các giá trị
                $phanLoai = '';
                if (!empty($sach['phan_loai_1']) || !empty($sach['phan_loai_2'])) {
                    $phanLoai = $sach['phan_loai_1'];
                    if (!empty($sach['phan_loai_1']) && !empty($sach['phan_loai_2'])) {
                        $phanLoai .= '/' . $sach['phan_loai_2'];
                    } elseif (empty($sach['phan_loai_1']) && !empty($sach['phan_loai_2'])) {
                        $phanLoai = $sach['phan_loai_2'];
                    }
                }
                
                $thongTinXuatBan = '';
                $arrXuatBan = [];
                if (!empty($sach['noi_xuat_ban'])) $arrXuatBan[] = $sach['noi_xuat_ban'];
                if (!empty($sach['nha_xuat_ban'])) $arrXuatBan[] = $sach['nha_xuat_ban'];
                if (!empty($sach['nam_xuat_ban'])) $arrXuatBan[] = $sach['nam_xuat_ban'];
                $thongTinXuatBan = implode(' - ', $arrXuatBan);
                
                $slDK = isset($sach['dkcb_list']) ? count($sach['dkcb_list']) : 0;
                $dkcbText = '';
                
                // Xử lý hiển thị DKCB tốt hơn để tránh xuống dòng không mong muốn
                if (isset($sach['dkcb_list']) && count($sach['dkcb_list']) > 0) {
                    $dkcbText = implode(', ', $sach['dkcb_list']);
                }
                
                // Cộng dồn tổng
                $tongSoLuong += $sach['so_luong'];
                $tongDKCB += $slDK;
                $tongThanhTien += $sach['thanh_tien'];
                
                // Điền dữ liệu vào hàng hiện tại
                $sheet->setCellValue('A' . $currentRow, $stt);
                $sheet->setCellValue('B' . $currentRow, $sach['id_don_nhan']);
                $sheet->setCellValue('C' . $currentRow, $sach['id_sach']);
                $sheet->setCellValue('D' . $currentRow, $sach['nhan_de']);
                $sheet->setCellValue('E' . $currentRow, $sach['tac_gia']);
                $sheet->setCellValue('F' . $currentRow, $thongTinXuatBan);
                $sheet->setCellValue('G' . $currentRow, $phanLoai);
                $sheet->setCellValue('H' . $currentRow, $sach['gia']);
                $sheet->getStyle('H' . $currentRow)->getNumberFormat()->setFormatCode('#,##0');
                $sheet->setCellValue('I' . $currentRow, $sach['so_luong']);
                $sheet->setCellValue('J' . $currentRow, $slDK);
                $sheet->setCellValue('K' . $currentRow, $dkcbText);
                $sheet->setCellValue('L' . $currentRow, $sach['thanh_tien']);
                $sheet->getStyle('L' . $currentRow)->getNumberFormat()->setFormatCode('#,##0');
                
                // Style cho cột DKCB - đảm bảo không xuống dòng tự động
                $sheet->getStyle('K' . $currentRow)->getAlignment()->setWrapText(false);
                
                // Style cho hàng
                $sheet->getStyle('A' . $currentRow . ':L' . $currentRow)->applyFromArray($borderStyle);
                $sheet->getStyle('A' . $currentRow . ':L' . $currentRow)->applyFromArray($defaultFont);
                
                // Wrap text và căn giữa dọc
                $sheet->getStyle('D' . $currentRow)->getAlignment()->setWrapText(true);
                $sheet->getStyle('E' . $currentRow)->getAlignment()->setWrapText(true);
                $sheet->getStyle('F' . $currentRow)->getAlignment()->setWrapText(true);
                $sheet->getStyle('G' . $currentRow)->getAlignment()->setWrapText(true);
                $sheet->getStyle('K' . $currentRow)->getAlignment()->setWrapText(true);
                
                $sheet->getStyle('A' . $currentRow . ':L' . $currentRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                
                // Tăng số thứ tự và chuyển sang hàng tiếp theo
                $stt++;
                $currentRow++;
            }
            
            // Thêm hàng tổng cộng
            $sheet->setCellValue('A' . $currentRow, 'Tổng cộng');
            $sheet->mergeCells('A' . $currentRow . ':H' . $currentRow);
            $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
            $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $sheet->setCellValue('I' . $currentRow, $tongSoLuong);
            $sheet->setCellValue('J' . $currentRow, $tongDKCB);
            $sheet->setCellValue('K' . $currentRow, '');
            $sheet->setCellValue('L' . $currentRow, $tongThanhTien);
            $sheet->getStyle('L' . $currentRow)->getNumberFormat()->setFormatCode('#,##0');
            
            $sheet->getStyle('A' . $currentRow . ':L' . $currentRow)->applyFromArray($borderStyle);
            $sheet->getStyle('A' . $currentRow . ':L' . $currentRow)->getFont()->setBold(true);
            $sheet->getStyle('A' . $currentRow . ':L' . $currentRow)->applyFromArray($defaultFont);
            
            // Thêm thông tin người lập báo cáo ở cuối
            $currentRow += 2;
            $now = Carbon::now()->format('d/m/Y');
            $sheet->setCellValue('J' . $currentRow, "Vĩnh Long, ngày " . Carbon::now()->day . " tháng " . Carbon::now()->month . " năm " . Carbon::now()->year);
            $sheet->mergeCells('J' . $currentRow . ':L' . $currentRow);
            $sheet->getStyle('J' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('J' . $currentRow)->applyFromArray($defaultFont);
            
            $currentRow += 1;
            $sheet->setCellValue('J' . $currentRow, "Người lập báo cáo");
            $sheet->mergeCells('J' . $currentRow . ':L' . $currentRow);
            $sheet->getStyle('J' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('J' . $currentRow)->getFont()->setBold(true);
            $sheet->getStyle('J' . $currentRow)->applyFromArray($defaultFont);
            
            $currentRow += 4;
            $sheet->setCellValue('J' . $currentRow, session('HoTen') ?? '');
            $sheet->mergeCells('J' . $currentRow . ':L' . $currentRow);
            $sheet->getStyle('J' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('J' . $currentRow)->applyFromArray($defaultFont);
            
            // Tên file xuất 
            $fileName = 'BC_Nhan_Sach_Phan_Kho_' . Carbon::now()->format('dmY_His') . '.xlsx';
            
            // Trả về file Excel
            $writer = new Xlsx($spreadsheet);
            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
            
        } catch (Throwable $e) {
            Log::error('Lỗi xuất báo cáo nhận sách phân kho: ' . $e->getMessage());
            return response()->json([
                'status' => 500, 
                'message' => 'Đã xảy ra lỗi khi xuất báo cáo: ' . $e->getMessage()
            ], 500);
        }
    }
}
