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
        Schema::create('finans_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('safe_id');
            $table->string('model_class');
            $table->string('model_id');
            $table->decimal('price', 10, 2);
            $table->string('process_type')->nullable();
            $table->timestamps();
            $table->string('payment_type')->nullable();
            $table->integer('currency_id')->nullable();
            $table->decimal('rate', 10, 2)->nullable();
            
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finans_transactions');
    }
};
