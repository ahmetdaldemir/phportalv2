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
        Schema::create('older_enumerations', function (Blueprint $table) {
            $table->id();
            $table->integer('enumeration_id');
            $table->integer('stock_card_movement_id');
            $table->string('serial', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('older_enumerations');
    }
};
