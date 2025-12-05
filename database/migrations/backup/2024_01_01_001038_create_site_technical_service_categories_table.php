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
        Schema::create('site_technical_service_categories', function (Blueprint $table) {
            $table->id();
            $table->string('category', 255);
            $table->string('title', 255);
            $table->text('sort_description')->nullable();
            $table->double('price');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_technical_service_categories');
    }
};
