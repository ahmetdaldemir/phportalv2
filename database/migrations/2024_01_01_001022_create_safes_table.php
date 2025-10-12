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
        Schema::create('safes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->foreignId('company_id');
            $table->foreignId('user_id');
            $table->foreignId('seller_id');
            $table->boolean('is_status')->default(1);
            $table->string('type', 20)->nullable();
            $table->double('incash');
            $table->double('outcash');
            $table->double('amount');
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->text('description')->nullable();
            $table->double('credit_card');
            $table->double('installment');
            $table->timestamps();
            $table->softDeletes();
            $table->index('invoice_id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('safes');
    }
};
