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
        Schema::create('technical_services', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('brand_id')->nullable();
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');

            $table->unsignedBigInteger('version_id')->nullable();
            $table->foreign('version_id')->references('id')->on('versions')->onDelete('cascade');

            $table->unsignedBigInteger('stock_id')->nullable();
            $table->foreign('stock_id')->references('id')->on('stock_cards')->onDelete('cascade');

            $table->unsignedBigInteger('stock_card_movement_id')->nullable();
            $table->foreign('stock_card_movement_id')->references('id')->on('stock_card_movements')->onDelete('cascade');


            $table->unsignedBigInteger('customer_id')->nullable();
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');

            $table->string('serial_number')->nullable();
            $table->string('status')->nullable();
            $table->double('total_price',10,2)->nullable();
            $table->double('customer_price',10,2)->nullable();
            $table->string('process_type',5)->nullable();
            $table->text('process')->nullable();
            $table->text('physical_condition')->nullable();
            $table->text('fault_information')->nullable();
            $table->text('accessories')->nullable();
            $table->string('device_password')->nullable();
            $table->string('imei')->nullable();

            $table->unsignedBigInteger('delivery_staff')->nullable();
            $table->foreign('delivery_staff')->references('id')->on('users')->onDelete('cascade');

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
        Schema::dropIfExists('technical_services');
    }
};
