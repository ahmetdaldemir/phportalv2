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
        Schema::table('transfers', function (Blueprint $table) {

            $table->unsignedBigInteger('main_seller_id')->nullable();
            $table->foreign('main_seller_id')->references('id')->on('sellers')->onDelete('cascade');


            $table->unsignedBigInteger('comfirm_id')->nullable();
            $table->foreign('comfirm_id')->references('id')->on('users')->onDelete('cascade');

            $table->date('comfirm_date')->nullable();

            $table->unsignedBigInteger('delivery_id')->nullable();
            $table->foreign('delivery_id')->references('id')->on('users')->onDelete('cascade');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transfers', function (Blueprint $table) {
            //
        });
    }
};
