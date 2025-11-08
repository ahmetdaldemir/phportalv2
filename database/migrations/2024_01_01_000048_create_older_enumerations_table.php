<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('older_enumerations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('enumeration_id');
            $table->unsignedBigInteger('stock_card_movement_id');
            $table->string('serial');
            $table->timestamps();
            
            $table->foreign('enumeration_id')->references('id')->on('enumerations');
            $table->foreign('stock_card_movement_id')->references('id')->on('stock_card_movements');
        });
    }

    public function down()
    {
        Schema::dropIfExists('older_enumerations');
    }
};
