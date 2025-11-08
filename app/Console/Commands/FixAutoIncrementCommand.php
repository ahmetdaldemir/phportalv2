<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixAutoIncrementCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'fix:autoincrement {--dry-run : Sadece kontrol et, değişiklik yapma}';

    /**
     * The console command description.
     */
    protected $description = 'Tüm tablolardaki AUTO_INCREMENT değerlerini düzeltir';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 AUTO_INCREMENT Kontrol ve Düzeltme Başlıyor...');
        $this->newLine();

        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->warn('⚠️  DRY RUN MODE - Hiçbir değişiklik yapılmayacak');
            $this->newLine();
        }

        try {
            // Tüm tabloları al
            $tables = $this->getAllTables();
            $this->info("📊 Toplam {$tables->count()} tablo bulundu");
            $this->newLine();

            $fixedCount = 0;
            $problemCount = 0;

            foreach ($tables as $table) {
                $result = $this->checkAndFixTable($table, $dryRun);
                
                if ($result['has_problem']) {
                    $problemCount++;
                    if ($result['fixed']) {
                        $fixedCount++;
                    }
                }
            }

            $this->newLine();
            $this->info("📈 ÖZET:");
            $this->info("   • Toplam tablo: {$tables->count()}");
            $this->info("   • Problemli tablo: {$problemCount}");
            
            if (!$dryRun) {
                $this->info("   • Düzeltilen tablo: {$fixedCount}");
            } else {
                $this->info("   • Düzeltilecek tablo: {$problemCount}");
            }

            if ($fixedCount > 0 || ($dryRun && $problemCount > 0)) {
                $this->newLine();
                $this->info('✅ İşlem tamamlandı!');
            } else {
                $this->info('✅ Tüm tablolar zaten doğru durumda!');
            }

        } catch (\Exception $e) {
            $this->error('❌ Hata: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Tüm tabloları al
     */
    private function getAllTables()
    {
        $database = DB::getDatabaseName();
        $tables = DB::select("SHOW TABLES FROM `{$database}`");
        
        return collect($tables)->map(function ($table) {
            $tableArray = (array) $table;
            return array_values($tableArray)[0];
        });
    }

    /**
     * Tabloyu kontrol et ve düzelt
     */
    private function checkAndFixTable($tableName, $dryRun = false)
    {
        $this->line("🔍 Kontrol ediliyor: <comment>{$tableName}</comment>");

        try {
            // Önce id kolonunun AUTO_INCREMENT olup olmadığını kontrol et
            $columns = DB::select("SHOW COLUMNS FROM `{$tableName}` WHERE Field = 'id'");
            
            if (empty($columns)) {
                $this->line("   ⏭️  'id' kolonu yok - atlanıyor");
                return ['has_problem' => false, 'fixed' => false];
            }

            $idColumn = $columns[0];
            $isAutoIncrement = strpos($idColumn->Extra, 'auto_increment') !== false;
            
            if (!$isAutoIncrement) {
                $this->warn("   ❌ Problem: 'id' kolonu AUTO_INCREMENT değil");
                
                if (!$dryRun) {
                    try {
                        // Maksimum ID'yi al
                        $maxId = DB::table($tableName)->max('id');
                        $nextId = $maxId ? $maxId + 1 : 1;
                        
                        // id kolonunu AUTO_INCREMENT yap (PRIMARY KEY zaten varsa sadece AUTO_INCREMENT ekle)
                        DB::statement("ALTER TABLE `{$tableName}` MODIFY COLUMN `id` INT AUTO_INCREMENT");
                        
                        // AUTO_INCREMENT değerini ayarla
                        DB::statement("ALTER TABLE `{$tableName}` AUTO_INCREMENT = {$nextId}");
                        
                        $this->info("   ✅ Düzeltildi: 'id' kolonu AUTO_INCREMENT yapıldı, başlangıç değeri = {$nextId}");
                        return ['has_problem' => true, 'fixed' => true];
                        
                    } catch (\Exception $e) {
                        $this->error("   ❌ SQL Hatası: " . $e->getMessage());
                        return ['has_problem' => true, 'fixed' => false];
                    }
                } else {
                    $this->info("   🔧 Düzeltilecek: 'id' kolonu AUTO_INCREMENT yapılacak");
                    return ['has_problem' => true, 'fixed' => false];
                }
            }

            // AUTO_INCREMENT olan tablolar için değer kontrolü
            $tableStatus = DB::select("SHOW TABLE STATUS LIKE '{$tableName}'");
            
            if (empty($tableStatus)) {
                $this->warn("   ⚠️  Tablo durumu alınamadı");
                return ['has_problem' => false, 'fixed' => false];
            }

            $status = $tableStatus[0];
            $autoIncrement = $status->Auto_increment;
            $engine = $status->Engine;

            // InnoDB olmayan tabloları atla
            if ($engine !== 'InnoDB') {
                $this->line("   ⏭️  {$engine} engine - atlanıyor");
                return ['has_problem' => false, 'fixed' => false];
            }

            // Maksimum ID'yi al
            $maxId = DB::table($tableName)->max('id');
            
            if ($maxId === null) {
                $this->line("   📭 Tablo boş");
                return ['has_problem' => false, 'fixed' => false];
            }

            $expectedAutoIncrement = $maxId + 1;

            // AUTO_INCREMENT değerini kontrol et (null veya düşük değer)
            if ($autoIncrement === null || $autoIncrement <= $maxId) {
                $autoIncrementDisplay = $autoIncrement === null ? 'NULL' : $autoIncrement;
                $this->warn("   ❌ Problem: AUTO_INCREMENT = {$autoIncrementDisplay}, MAX ID = {$maxId}");
                
                if (!$dryRun) {
                    try {
                        // Düzelt - NULL durumunda da çalışacak
                        DB::statement("ALTER TABLE `{$tableName}` AUTO_INCREMENT = {$expectedAutoIncrement}");
                        
                        // Kontrol et
                        $newStatus = DB::select("SHOW TABLE STATUS LIKE '{$tableName}'");
                        $newAutoIncrement = $newStatus[0]->Auto_increment;
                        
                        if ($newAutoIncrement == $expectedAutoIncrement) {
                            $this->info("   ✅ Düzeltildi: AUTO_INCREMENT = {$newAutoIncrement}");
                            return ['has_problem' => true, 'fixed' => true];
                        } else {
                            $this->error("   ❌ Düzeltme başarısız! Yeni değer: {$newAutoIncrement}");
                            return ['has_problem' => true, 'fixed' => false];
                        }
                    } catch (\Exception $e) {
                        $this->error("   ❌ SQL Hatası: " . $e->getMessage());
                        return ['has_problem' => true, 'fixed' => false];
                    }
                } else {
                    $this->info("   🔧 Düzeltilecek: AUTO_INCREMENT = {$expectedAutoIncrement}");
                    return ['has_problem' => true, 'fixed' => false];
                }
            } else {
                $this->line("   ✅ OK: AUTO_INCREMENT = {$autoIncrement}, MAX ID = {$maxId}");
                return ['has_problem' => false, 'fixed' => false];
            }

        } catch (\Exception $e) {
            $this->error("   ❌ Hata: " . $e->getMessage());
            return ['has_problem' => false, 'fixed' => false];
        }
    }
}
