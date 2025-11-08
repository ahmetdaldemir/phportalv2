<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('seller_account_months', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('mounth');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('seller_id');
            $table->decimal('rent', 10, 2);
            $table->decimal('invoice', 10, 2);
            $table->decimal('tax', 10, 2);
            $table->decimal('additional_expense', 10, 2);
            $table->timestamps();
            
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('seller_id')->references('id')->on('sellers');
        });
    }

    public function down()
    {
        Schema::dropIfExists('seller_account_months');
    }
};
