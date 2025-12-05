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
        Schema::create('technical_service_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('parent_id', 11)->default(0);
            $table->foreignId('company_id');
            $table->foreignId('user_id');
            $table->boolean('is_status')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('technical_service_categories');
    }
};
