<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stock_cards', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->text('category_id');
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->string('version_id')->nullable();
            $table->string('sku', 20);
            $table->string('barcode', 20);
            $table->boolean('tracking')->default(0);
            $table->string('unit', 5)->nullable();
            $table->tinyInteger('tracking_quantity')->default(1);
            $table->boolean('is_status')->default(1);
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('company_id');
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('brand_id')->references('id')->on('brands');
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_cards');
    }
};
