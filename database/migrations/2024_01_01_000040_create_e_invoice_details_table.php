<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('e_invoice_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('e_invoice_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('stock_card_id');
            $table->string('name');
            $table->string('quantity', 4);
            $table->double('price');
            $table->decimal('taxPrice', 10, 2);
            $table->decimal('tax', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->timestamps();
            
            $table->foreign('e_invoice_id')->references('id')->on('e_invoices');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('stock_card_id')->references('id')->on('stock_cards');
        });
    }

    public function down()
    {
        Schema::dropIfExists('e_invoice_details');
    }
};
