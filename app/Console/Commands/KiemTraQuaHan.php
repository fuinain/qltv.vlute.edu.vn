<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MuonSachModel;
use App\Models\DocTaiChoModel;
use Carbon\Carbon;

class KiemTraQuaHan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:kiem-tra-qua-han';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kiểm tra và cập nhật tình trạng quá hạn cho sách mượn và đọc tại chỗ';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $this->info('Bắt đầu kiểm tra quá hạn: ' . $now->format('Y-m-d H:i:s'));

        // Kiểm tra sách mượn quá hạn
        $sachMuon = MuonSachModel::where('qua_han', 0)
            ->whereDate('han_tra', '<', $now->format('Y-m-d'))
            ->get();

        $countSachMuon = 0;
        foreach ($sachMuon as $sach) {
            $sach->qua_han = 1; 
            $sach->save();
            $countSachMuon++;
        }
        $this->info("Đã cập nhật {$countSachMuon} sách mượn quá hạn");

        // Kiểm tra sách đọc tại chỗ quá hạn
        // Nếu đã quá 16h30 ngày hiện tại, tất cả sách chưa trả đều quá hạn
        $thoiGianGioiHan = Carbon::now()->setHour(16)->setMinute(30)->setSecond(0);
        
        $sachDocTaiCho = DocTaiChoModel::where('qua_han', 0);
        
        if ($now->greaterThan($thoiGianGioiHan)) {
            // Chỉ cập nhật những sách mượn trong ngày hôm nay
            $sachDocTaiCho = $sachDocTaiCho
                ->whereDate('gio_muon', $now->format('Y-m-d'))
                ->where('gio_tra', '<', $now->format('Y-m-d H:i:s'))
                ->get();
        } else {
            // Cập nhật tất cả các sách từ những ngày trước
            $sachDocTaiCho = $sachDocTaiCho
                ->whereDate('gio_muon', '<', $now->format('Y-m-d'))
                ->get();
        }
        
        $countSachDocTaiCho = 0;
        foreach ($sachDocTaiCho as $sach) {
            $sach->qua_han = 1;
            $sach->save();
            $countSachDocTaiCho++;
        }
        $this->info("Đã cập nhật {$countSachDocTaiCho} sách đọc tại chỗ quá hạn");
        
        $this->info('Hoàn thành kiểm tra quá hạn');
        
        return 0;
    }
} 