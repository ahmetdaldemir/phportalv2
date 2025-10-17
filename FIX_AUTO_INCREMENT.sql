-- ============================================================================
-- FIX AUTO_INCREMENT for stock_card_movements table
-- ============================================================================
-- 
-- Problem: "Duplicate entry '0' for key 'PRIMARY'" hatası alınıyor
-- Sebep: AUTO_INCREMENT değeri 0 veya çok düşük bir değere ayarlanmış
-- 
-- Bu SQL komutunu production veritabanında çalıştırın:
-- ============================================================================

-- 1. Mevcut maksimum ID'yi kontrol et
SELECT MAX(id) as max_id FROM stock_card_movements;

-- 2. AUTO_INCREMENT değerini maksimum ID + 1 olarak ayarla
-- (Yukarıdaki sorgudan dönen değeri +1 ekleyerek yazın)
-- Örnek: Eğer MAX(id) = 150000 ise:
ALTER TABLE `stock_card_movements` AUTO_INCREMENT = 150001;

-- ============================================================================
-- VEYA tek komutla otomatik düzelt:
-- ============================================================================

SET @max_id = (SELECT MAX(id) FROM stock_card_movements);
SET @next_id = @max_id + 1;
SET @sql = CONCAT('ALTER TABLE `stock_card_movements` AUTO_INCREMENT = ', @next_id);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Sonucu kontrol et:
SHOW TABLE STATUS LIKE 'stock_card_movements';
-- Auto_increment kolonundaki değer doğru olmalı

-- ============================================================================
-- Sorun tekrar ederse:
-- ============================================================================
-- Model'de ID manuel olarak set edilip edilmediğini kontrol edin
-- StockCardMovement model'inde bu özellikler kontrol edilmeli:
-- public $incrementing = true;  // default true olmalı
-- protected $primaryKey = 'id'; // default 'id' olmalı
-- ============================================================================

