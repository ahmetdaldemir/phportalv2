#!/bin/bash

# Migration Creation Script
# Bu script SQL schema'ya göre tüm migration'ları oluşturur

echo "🚀 Migration oluşturma başlıyor..."
echo "================================"

# Laravel artisan komutlarını kullanarak migration'lar oluştur

echo "📦 Spatie Permission tabloları..."
php artisan make:migration create_permissions_table
php artisan make:migration create_roles_table  
php artisan make:migration create_model_has_permissions_table
php artisan make:migration create_model_has_roles_table
php artisan make:migration create_role_has_permissions_table
php artisan make:migration create_personal_access_tokens_table

echo "📦 Master Data tabloları..."
php artisan make:migration create_brands_table
php artisan make:migration create_categories_table
php artisan make:migration create_colors_table
php artisan make:migration create_warehouses_table
php artisan make:migration create_reasons_table
php artisan make:migration create_versions_table
php artisan make:migration create_version_children_table

echo "📦 Müşteri & Finans tabloları..."
php artisan make:migration create_customers_table
php artisan make:migration create_banks_table
php artisan make:migration create_safes_table
php artisan make:migration create_accounting_categories_table
php artisan make:migration create_accountings_table

echo "📦 Stok Yönetimi tabloları..."
php artisan make:migration create_stock_cards_table
php artisan make:migration create_stock_card_movements_table
php artisan make:migration create_stock_card_prices_table

echo "📦 Satış & Fatura tabloları..."
php artisan make:migration create_invoices_table
php artisan make:migration create_sales_table
php artisan make:migration create_phones_table

echo "📦 Transfer & Demand tabloları..."
php artisan make:migration create_transfers_table
php artisan make:migration create_demands_table
php artisan make:migration create_refunds_table

echo "📦 Teknik Servis tabloları..."
php artisan make:migration create_technical_service_categories_table
php artisan make:migration create_technical_services_table
php artisan make:migration create_technical_service_processes_table
php artisan make:migration create_technical_service_products_table
php artisan make:migration create_technical_custom_services_table
php artisan make:migration create_technical_custom_products_table
php artisan make:migration create_site_technical_service_categories_table

echo "📦 E-Fatura tabloları..."
php artisan make:migration create_e_invoices_table
php artisan make:migration create_e_invoice_details_table

echo "📦 Finans İşlemleri tabloları..."
php artisan make:migration create_finans_transactions_table
php artisan make:migration create_personal_account_months_table
php artisan make:migration create_seller_account_months_table
php artisan make:migration create_user_sallaries_table

echo "📦 Diğer tablolar..."
php artisan make:migration create_notifications_table
php artisan make:migration create_enumerations_table
php artisan make:migration create_older_enumerations_table
php artisan make:migration create_stock_trakings_table
php artisan make:migration create_blogs_table
php artisan make:migration create_settings_table
php artisan make:migration create_fake_products_table
php artisan make:migration create_remote_api_logs_table
php artisan make:migration create_deleted_at_serial_numbers_table
php artisan make:migration create_activity_log_table
php artisan make:migration create_laravel_logger_activity_table

echo "📦 Laravel System tabloları..."
php artisan make:migration create_failed_jobs_table
php artisan make:migration create_jobs_table

echo "================================"
echo "✅ Tüm migration'lar oluşturuldu!"
echo "📝 Şimdi her migration dosyasını SQL schema'ya göre doldurmanız gerekiyor."

