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
        Schema::create('stock_cards', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->text('category_id')->nullable();
            $table->unsignedBigInteger('brand_id');
            $table->string('version_id', 255)->nullable();
            $table->string('sku', 20)->nullable();
            $table->string('barcode', 20)->nullable();
            $table->boolean('tracking')->default(0);
            $table->string('unit', 5)->nullable();
            $table->tinyInteger('tracking_quantity')->nullable()->default(1);
            $table->boolean('is_status')->default(1);
            $table->foreignId('user_id');
            $table->foreignId('company_id');
            $table->timestamps();
            $table->softDeletes();
            $table->index('version_id');
            $table->index('brand_id');
            $table->foreign('brand_id')->references('id')->on('brands')->onUpdate('cascade');
            $table->foreign('company_id')->references('id')->on('companies')->onUpdate('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_cards');
    }
};
