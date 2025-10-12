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
        Schema::create('remote_api_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('request_class', 512);
            $table->string('remote_path', 2048);
            $table->integer('http_status')->default(0);
            $table->longText('request')->nullable();
            $table->longText('response')->nullable();
            $table->longText('errors')->nullable();
            $table->boolean('failed')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remote_api_logs');
    }
};
