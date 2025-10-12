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
        Schema::create('e_invoice_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('e_invoice_id');
            $table->foreignId('user_id');
            $table->foreignId('company_id');
            $table->unsignedBigInteger('stock_card_id');
            $table->string('name', 255);
            $table->string('quantity', 4);
            $table->double('price');
            $table->double('taxPrice');
            $table->double('tax');
            $table->double('total_price');
            $table->timestamps();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('e_invoice_id')->references('id')->on('e_invoices')->onDelete('cascade');
            $table->foreign('stock_card_id')->references('id')->on('stock_cards')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('e_invoice_details');
    }
};
