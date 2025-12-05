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
        Schema::create('seller_account_months', function (Blueprint $table) {
            $table->id();
            $table->date('mounth')->nullable();
            $table->foreignId('company_id');
            $table->foreignId('seller_id');
            $table->double('rent');
            $table->double('invoice');
            $table->double('tax');
            $table->double('additional_expense');
            $table->timestamps();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('seller_id')->references('id')->on('sellers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_account_months');
    }
};
