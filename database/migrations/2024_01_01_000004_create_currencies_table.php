<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->string('name');
            $table->string('code', 10);
            $table->string('symbol', 25);
            $table->string('format', 50);
            $table->string('exchange_rate');
            $table->boolean('active')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('currencies');
    }
};
