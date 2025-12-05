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
        Schema::table('laravel_logger_activity', function (Blueprint $table) {
            $table->string('relModel')->nullable()->after('userId');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laravel_logger_activity', function (Blueprint $table) {
            $table->dropColumn('relModel');
        });
    }
};
