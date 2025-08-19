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
        Schema::create('transfers', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('stock_card_id');
            $table->foreign('stock_card_id')->references('id')->on('stock_cards')->onDelete('cascade');

            $table->string('serial_number');

            $table->unsignedBigInteger('stock_card_movement_id');
            $table->foreign('stock_card_movement_id')->references('id')->on('stock_card_movements')->onDelete('cascade');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');


            $table->string('type')->default('other');
            $table->text('detail')->nullable();
            $table->tinyInteger('is_status')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transfers');
    }
};
