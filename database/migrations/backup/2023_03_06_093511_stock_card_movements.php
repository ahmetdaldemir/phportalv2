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
            $table->id();
            $table->unsignedBigInteger('stock_card_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('color_id');
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->unsignedBigInteger('seller_id')->nullable();
            $table->unsignedBigInteger('reason_id')->nullable();
            $table->mediumInteger('type')->nullable();
            $table->mediumInteger('quantity');
            $table->string('serial_number', 50)->nullable();
            $table->string('tax')->default('18');
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->decimal('base_cost_price', 10, 2)->nullable();
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->float('discount', 10, 2)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->string('imei')->nullable();
            $table->boolean('assigned_accessory')->default(0);
            $table->boolean('tracking_quantity')->default(0);
            $table->boolean('assigned_device')->nullable();
            $table->bigInteger('company_id')->default(1);
            $table->string('prefix', 3)->nullable();
            
            $table->foreign('stock_card_id')->references('id')->on('stock_cards')->onDelete('no action');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('no action');
            $table->foreign('color_id')->references('id')->on('colors')->onDelete('no action');
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('no action');
            $table->foreign('seller_id')->references('id')->on('sellers')->onDelete('no action');
            $table->foreign('reason_id')->references('id')->on('reasons')->onDelete('no action');
            $table->index('id');
            $table->index('serial_number');
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
