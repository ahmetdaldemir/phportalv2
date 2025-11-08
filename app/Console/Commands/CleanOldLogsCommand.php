<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class CleanOldLogsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:clean {--days=7 : Kaç günden eski logları sil}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Eski log dosyalarını temizler (saatlik loglar için)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $logPath = storage_path('logs');
        
        $this->info("🧹 {$days} günden eski log dosyaları temizleniyor...");

        $cutoffDate = Carbon::now()->subDays($days);
        $deletedCount = 0;
        $totalSize = 0;

        // Log klasöründeki tüm dosyaları kontrol et
        $files = File::glob($logPath . '/laravel-*.log');

        foreach ($files as $file) {
            $fileTime = File::lastModified($file);
            $fileDate = Carbon::createFromTimestamp($fileTime);
            
            if ($fileDate->isBefore($cutoffDate)) {
                $fileSize = File::size($file);
                $totalSize += $fileSize;
                
                $fileName = basename($file);
                
                if (File::delete($file)) {
                    $this->line("  ✓ Silindi: {$fileName} (" . $this->formatBytes($fileSize) . ")");
                    $deletedCount++;
                } else {
                    $this->error("  ✗ Silinemedi: {$fileName}");
                }
            }
        }

        if ($deletedCount > 0) {
            $this->info("✅ {$deletedCount} log dosyası silindi");
            $this->info("💾 Toplam {$this->formatBytes($totalSize)} disk alanı temizlendi");
        } else {
            $this->info("📁 Silinecek eski log dosyası bulunamadı");
        }

        // Mevcut log dosyalarını göster
        $remainingFiles = File::glob($logPath . '/laravel-*.log');
        if (count($remainingFiles) > 0) {
            $this->info("\n📋 Kalan log dosyaları:");
            foreach ($remainingFiles as $file) {
                $fileName = basename($file);
                $fileSize = $this->formatBytes(File::size($file));
                $fileDate = Carbon::createFromTimestamp(File::lastModified($file))->format('Y-m-d H:i');
                $this->line("  • {$fileName} ({$fileSize}) - {$fileDate}");
            }
        }

        return Command::SUCCESS;
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
