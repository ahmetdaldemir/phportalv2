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
        Schema::create('sellers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id');
            $table->string('name', 255);
            $table->string('phone', 255)->nullable();
            $table->boolean('is_status')->default(1);
            $table->foreignId('user_id');
            $table->boolean('can_see_stock')->default(1);
            $table->boolean('can_see_cost_price')->default(1);
            $table->boolean('can_see_base_cost_price')->default(1);
            $table->boolean('can_see_sale_price')->default(1);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sellers');
    }
};
