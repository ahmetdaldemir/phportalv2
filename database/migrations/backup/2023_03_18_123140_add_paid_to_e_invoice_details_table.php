<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('e_invoice_details', function (Blueprint $table) {
            $table->unsignedBigInteger('stock_card_id');
            $table->foreign('stock_card_id')->references('id')->on('stock_cards')->onDelete('cascade');
            $table->string('name');
            $table->string('quantity',4);
            $table->double('price',4);
            $table->double('taxPrice',10,2);
            $table->double('tax',10,2);
            $table->double('total_price',10,2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('e_invoice_details', function (Blueprint $table) {
            //
        });
    }
};
