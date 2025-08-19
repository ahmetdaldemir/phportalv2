<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('safes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('seller_id')->nullable();
            $table->boolean('is_status')->default(1);
            $table->string('type', 20)->nullable();
            $table->decimal('incash', 10, 2)->default(0);
            $table->decimal('outcash', 10, 2)->default(0);
            $table->decimal('amount', 10, 2)->default(0);
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->text('description')->nullable();
            $table->decimal('credit_card', 10, 2)->default(0);
            $table->decimal('installment', 10, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('seller_id')->references('id')->on('sellers');
            // invoice_id foreign key will be added after invoices table creation
        });
    }

    public function down()
    {
        Schema::dropIfExists('safes');
    }
};
