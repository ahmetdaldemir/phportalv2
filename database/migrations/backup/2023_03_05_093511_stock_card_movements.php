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
        Schema::create('stock_card_movements', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('stock_card_id');
            $table->foreign('stock_card_id')->references('id')->on('stock_cards')->onDelete('cascade');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('color_id');
            $table->foreign('color_id')->references('id')->on('colors')->onDelete('cascade');


            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');

            $table->unsignedBigInteger('seller_id')->nullable();
            $table->foreign('seller_id')->references('id')->on('sellers')->onDelete('cascade');

            $table->unsignedBigInteger('reason_id')->nullable();
            $table->foreign('reason_id')->references('id')->on('reasons')->onDelete('cascade');


            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');


            $table->mediumInteger('type');
            $table->mediumInteger('quantity');
            $table->string('serial_number',50)->nullable();
            $table->string('barcode',50)->nullable();
            $table->string('tax')->default(18);
            $table->string('tracking_quantity')->default(1);


            $table->double('cost_price',10,2)->nullable();
            $table->double('base_cost_price',10,2)->nullable();
            $table->double('sale_price',10,2)->nullable();

            $table->text('description')->nullable();



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
        Schema::dropIfExists('stock_card_movements');
    }
};
