<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code');
            $table->string('fullname');
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('tc')->nullable();
            $table->string('iban')->nullable();
            $table->string('phone1')->nullable();
            $table->string('phone2')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->string('email')->nullable();
            $table->text('note')->nullable();
            $table->enum('type', ['customer', 'account', 'sitecustomer'])->default('customer');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('seller_id');
            $table->unsignedBigInteger('user_id');
            $table->boolean('is_status')->default(1);
            $table->boolean('is_danger')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('seller_id')->references('id')->on('sellers');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('customers');
    }
};
