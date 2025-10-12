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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('type');
            $table->string('number', 255)->nullable();
            $table->date('create_date')->nullable();
            $table->text('description')->nullable();
            $table->string('is_status', 255);
            $table->double('total_price');
            $table->integer('customer_id')->nullable();
            $table->foreignId('user_id');
            $table->integer('staff_id')->nullable();
            $table->foreignId('company_id');
            $table->double('tax_total');
            $table->double('discount_total');
            $table->double('exchange');
            $table->double('tax');
            $table->string('file', 255)->nullable();
            $table->string('paymentStatus');
            $table->date('paymentDate')->nullable();
            $table->string('paymentStaff', 255)->nullable();
            $table->string('periodMounth', 10)->nullable();
            $table->year('periodYear')->nullable();
            $table->unsignedBigInteger('accounting_category_id');
            $table->double('currency');
            $table->integer('safe_id')->nullable();
            $table->double('credit_card');
            $table->double('cash');
            $table->double('installment');
            $table->text('detail')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
