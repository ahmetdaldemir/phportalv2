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
        Schema::create('personal_account_months', function (Blueprint $table) {
            $table->id();
            $table->date('mounth')->nullable();
            $table->foreignId('company_id');
            $table->unsignedBigInteger('staff_id');
            $table->double('salary');
            $table->double('overtime');
            $table->double('way');
            $table->double('meal');
            $table->double('bounty');
            $table->double('insurance');
            $table->timestamps();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('staff_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_account_months');
    }
};
