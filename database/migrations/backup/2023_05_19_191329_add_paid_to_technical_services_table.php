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
        Schema::table('technical_services', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_person')->default(1);
            $table->foreign('payment_person')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('technical_person')->default(1);
            $table->foreign('technical_person')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('technical_services', function (Blueprint $table) {
            //
        });
    }
};
