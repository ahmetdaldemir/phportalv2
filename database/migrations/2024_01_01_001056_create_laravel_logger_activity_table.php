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
        Schema::create('laravel_logger_activity', function (Blueprint $table) {
            $table->increments('id');
            $table->longText('description');
            $table->longText('details')->nullable();
            $table->string('userType', 255);
            $table->integer('userId')->nullable();
            $table->longText('route')->nullable();
            $table->string('ipAddress', 45)->nullable();
            $table->text('userAgent')->nullable();
            $table->string('locale', 255)->nullable();
            $table->longText('referer')->nullable();
            $table->string('methodType', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laravel_logger_activity');
    }
};
