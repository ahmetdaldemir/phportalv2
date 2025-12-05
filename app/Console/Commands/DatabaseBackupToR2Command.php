<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DatabaseBackupToR2Command extends Command
{
    protected $signature = 'db:backup-r2 
                            {--keep=30 : Kaç günlük yedek tutulacak (varsayılan: 30)}
                            {--compress : Yedeği gzip ile sıkıştır}';

    protected $description = 'Veritabanı yedeğini alır ve Cloudflare R2\'ye yükler';

    private ?string $configFile = null;
    private ?string $backupFile = null;

    public function handle(): int
    {
        $this->info('🚀 Veritabanı yedekleme işlemi başlatılıyor...');
        $this->newLine();

        try {
            $connection = Config::get('database.default');
            $dbConfig = Config::get("database.connections.{$connection}");
            
            $dbName = $dbConfig['database'];
            $dbUser = $dbConfig['username'];
            $dbPass = $dbConfig['password'] ?? '';
            $dbHost = $dbConfig['host'];
            $dbPort = $dbConfig['port'] ?? 3306;

            $backupDir = storage_path('app/backups');
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
            $filename = "backup_{$dbName}_{$timestamp}.sql";
            $this->backupFile = "{$backupDir}/{$filename}";
            
            $compress = $this->option('compress');
            if ($compress) {
                $filename .= '.gz';
                $this->backupFile .= '.gz';
            }

            $this->info("📦 Veritabanı: {$dbName}");
            $this->info("📁 Dosya: {$filename}");
            $this->newLine();

            $bar = $this->output->createProgressBar(4);
            $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');
            $bar->setMessage('Yedek alınıyor...');
            $bar->start();

            $this->configFile = $this->createConfigFile($dbHost, $dbPort, $dbUser, $dbPass);

            $command = $this->buildDumpCommand($dbName, $compress);

            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                $bar->finish();
                $this->newLine(2);
                $this->error('❌ Yedek alma hatası!');
                $this->error('Return Code: ' . $returnCode);
                $this->error('Hata Detayı: ' . implode("\n", $output));
                Log::error('Database backup failed', [
                    'return_code' => $returnCode,
                    'output' => $output,
                    'command' => preg_replace('/--defaults-extra-file=[^\s]+/', '--defaults-extra-file=***', $command)
                ]);
                return Command::FAILURE;
            }

            if (!file_exists($this->backupFile) || filesize($this->backupFile) === 0) {
                $bar->finish();
                $this->newLine(2);
                $this->error('❌ Yedek dosyası oluşturulamadı veya boş!');
                $this->error('Dosya yolu: ' . $this->backupFile);
                Log::error('Database backup file is empty or missing', [
                    'file_path' => $this->backupFile,
                    'exists' => file_exists($this->backupFile),
                    'size' => file_exists($this->backupFile) ? filesize($this->backupFile) : 0
                ]);
                return Command::FAILURE;
            }

            $fileSize = $this->formatBytes(filesize($this->backupFile));
            $bar->setMessage("Yedek alındı ({$fileSize})");
            $bar->advance();

            $bar->setMessage('R2\'ye yükleniyor...');
            $bar->advance();

            $r2Path = "backups/{$filename}";
            
            try {
                $this->uploadToR2($r2Path);
                $bar->setMessage('R2\'ye yüklendi');
                $bar->advance();
            } catch (\Exception $e) {
                $bar->finish();
                $this->newLine(2);
                $this->error('❌ R2\'ye yükleme hatası!');
                $this->error('Hata: ' . $e->getMessage());
                $this->error('Dosya: ' . $e->getFile() . ':' . $e->getLine());
                Log::error('R2 upload failed', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);
                return Command::FAILURE;
            }

            $bar->setMessage('Eski yedekler temizleniyor...');
            $bar->advance();
            
            $keepDays = (int) $this->option('keep');
            $this->cleanOldBackups($keepDays);

            $bar->setMessage('Tamamlandı!');
            $bar->finish();
            $this->newLine(2);

            $this->info("✅ Yedekleme başarıyla tamamlandı!");
            $this->info("📤 R2 Path: {$r2Path}");
            $this->info("💾 Dosya Boyutu: {$fileSize}");
            
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->newLine(2);
            $this->error('❌ Beklenmeyen hata oluştu!');
            $this->error('Hata: ' . $e->getMessage());
            $this->error('Dosya: ' . $e->getFile() . ':' . $e->getLine());
            Log::error('Database backup unexpected error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;
        } finally {
            $this->cleanup();
        }
    }

    private function createConfigFile(string $host, string $port, string $user, string $pass): string
    {
        $configFile = storage_path('app/.db_' . uniqid() . '.cnf');
        $content = "[client]\n";
        $content .= "host=" . $host . "\n";
        $content .= "port=" . $port . "\n";
        $content .= "user=" . $user . "\n";
        
        if (!empty($pass)) {
            $content .= "password=" . $pass . "\n";
        }

        file_put_contents($configFile, $content);
        chmod($configFile, 0600);

        return $configFile;
    }

    private function buildDumpCommand(string $dbName, bool $compress): string
    {
        $baseCommand = sprintf(
            'mysqldump --defaults-extra-file=%s --single-transaction --quick --skip-lock-tables %s',
            escapeshellarg($this->configFile),
            escapeshellarg($dbName)
        );

        if ($compress) {
            return $baseCommand . ' | gzip > ' . escapeshellarg($this->backupFile) . ' 2>&1';
        }

        return $baseCommand . ' > ' . escapeshellarg($this->backupFile) . ' 2>&1';
    }

    private function uploadToR2(string $r2Path): void
    {
        $stream = fopen($this->backupFile, 'r');
        
        if ($stream === false) {
            throw new \RuntimeException("Yedek dosyası açılamadı: {$this->backupFile}");
        }

        try {
            Storage::disk('r2')->writeStream($r2Path, $stream);
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
        }
    }

    protected function cleanOldBackups(int $keepDays): void
    {
        try {
            $files = Storage::disk('r2')->files('backups');
            $cutoffDate = Carbon::now()->subDays($keepDays);

            foreach ($files as $file) {
                $lastModified = Carbon::parse(Storage::disk('r2')->lastModified($file));
                
                if ($lastModified->lt($cutoffDate)) {
                    Storage::disk('r2')->delete($file);
                    $this->line("🗑️  Eski yedek silindi: " . basename($file));
                }
            }
        } catch (\Exception $e) {
            $this->warn("⚠️  Eski yedekler temizlenirken hata: " . $e->getMessage());
            Log::warning('Old backups cleanup failed', [
                'message' => $e->getMessage(),
                'keep_days' => $keepDays
            ]);
        }
    }

    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    private function cleanup(): void
    {
        if ($this->configFile !== null && file_exists($this->configFile)) {
            @unlink($this->configFile);
        }

        if ($this->backupFile !== null && file_exists($this->backupFile)) {
            @unlink($this->backupFile);
        }
    }
}
