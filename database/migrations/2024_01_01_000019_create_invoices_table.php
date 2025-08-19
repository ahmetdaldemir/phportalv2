<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('type');
            $table->string('number');
            $table->date('create_date');
            $table->text('description')->nullable();
            $table->string('is_status');
            $table->double('total_price');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('staff_id')->nullable();
            $table->unsignedBigInteger('company_id');
            $table->decimal('tax_total', 10, 2)->default(0);
            $table->decimal('discount_total', 10, 2)->default(0);
            $table->decimal('exchange', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->string('file')->nullable();
            $table->enum('paymentStatus', ['unpaid', 'paid', 'paidoutofpocket'])->default('unpaid')->comment('paidOutOfPocket=~Personel Cebinden Ödedi');
            $table->date('paymentDate')->nullable();
            $table->string('paymentStaff')->nullable();
            $table->string('periodMounth', 10)->nullable();
            $table->year('periodYear')->nullable();
            $table->unsignedBigInteger('accounting_category_id')->nullable();
            $table->decimal('currency', 10, 2)->default(0)->comment('Vergiler Dahil');
            $table->unsignedBigInteger('safe_id')->nullable();
            $table->decimal('credit_card', 10, 2)->default(0);
            $table->decimal('cash', 10, 2)->default(0);
            $table->decimal('installment', 10, 2)->default(0);
            $table->text('detail')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('accounting_category_id')->references('id')->on('accounting_categories');
            $table->foreign('safe_id')->references('id')->on('safes');
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoices');
    }
};
