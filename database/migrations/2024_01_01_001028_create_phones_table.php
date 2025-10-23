<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('phones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('company_id');
            $table->unsignedBigInteger('brand_id');
            $table->unsignedBigInteger('version_id');
            $table->unsignedBigInteger('color_id');
            $table->foreignId('seller_id');
            $table->integer('quantity')->default(0);
            $table->string('type', 255)->nullable();
            $table->string('imei', 15)->nullable();
            $table->string('barcode', 255)->nullable();
            $table->text('description')->nullable();
            $table->double('cost_price')->default(0);
            $table->double('sale_price')->default(0);
            $table->unsignedBigInteger('customer_id');
            $table->text('altered_parts')->nullable();
            $table->text('physical_condition')->nullable();
            $table->string('memory', 255)->nullable();
            $table->string('battery', 255)->nullable();
            $table->string('warranty', 255)->nullable();
            $table->boolean('status')->default(0);
            $table->integer('invoice_id')->nullable();
            $table->boolean('is_confirm')->default(0);
            $table->string('sales_person', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');
            $table->foreign('color_id')->references('id')->on('colors')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('seller_id')->references('id')->on('sellers')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('version_id')->references('id')->on('versions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phones');
    }
};
