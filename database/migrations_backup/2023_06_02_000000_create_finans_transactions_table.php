<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('finans_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('safe_id');
            $table->string('model_class');
            $table->string('model_id');
            $table->decimal('price', 10, 2);
            $table->string('process_type');
            $table->string('payment_type');
            $table->integer('currency_id');
            $table->decimal('rate', 10, 2);
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('safe_id')->references('id')->on('safes');
            $table->foreign('currency_id')->references('id')->on('currencies');
        });
    }

    public function down()
    {
        Schema::dropIfExists('finans_transactions');
    }
};
