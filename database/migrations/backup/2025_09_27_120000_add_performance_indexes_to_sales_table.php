<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            // Performance indexes for Sale Controller queries
            $table->index(['company_id', 'created_at'], 'sales_company_date_idx');
            $table->index(['invoice_id', 'type'], 'sales_invoice_type_idx');
            $table->index(['seller_id', 'created_at'], 'sales_seller_date_idx');
            $table->index(['stock_card_movement_id', 'type'], 'sales_movement_type_idx');
        });
        
        Schema::table('invoices', function (Blueprint $table) {
            // Invoice performance indexes
            $table->index(['company_id', 'created_at'], 'invoices_company_date_idx');
            $table->index(['customer_id', 'type'], 'invoices_customer_type_idx');
        });
        
        Schema::table('stock_card_movements', function (Blueprint $table) {
            // Stock movement performance indexes
            $table->index(['stock_card_id', 'created_at'], 'movements_stock_date_idx');
            $table->index(['invoice_id', 'type'], 'movements_invoice_type_idx');
            $table->index(['serial_number'], 'movements_serial_idx');
        });
        
        Schema::table('phones', function (Blueprint $table) {
            // Phone performance indexes
            $table->index(['company_id', 'created_at'], 'phones_company_date_idx');
            $table->index(['cost_price'], 'phones_cost_price_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex('sales_company_date_idx');
            $table->dropIndex('sales_invoice_type_idx');
            $table->dropIndex('sales_seller_date_idx');
            $table->dropIndex('sales_movement_type_idx');
        });
        
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex('invoices_company_date_idx');
            $table->dropIndex('invoices_customer_type_idx');
        });
        
        Schema::table('stock_card_movements', function (Blueprint $table) {
            $table->dropIndex('movements_stock_date_idx');
            $table->dropIndex('movements_invoice_type_idx');
            $table->dropIndex('movements_serial_idx');
        });
        
        Schema::table('phones', function (Blueprint $table) {
            $table->dropIndex('phones_company_date_idx');
            $table->dropIndex('phones_cost_price_idx');
        });
    }
};
