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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('phone', 255)->nullable();
            $table->string('authorized', 255)->nullable();
            $table->string('web_sites', 100)->nullable();
            $table->string('commercial_registration_number', 50)->nullable()->comment('Ticari Sicil No');
            $table->string('tax_number', 50)->nullable()->comment('Vergi No');
            $table->string('tax_office', 150)->nullable()->comment('Vergi Dairesi');
            $table->string('mersis_number', 50)->nullable()->comment('Mersis No');
            $table->string('company_name', 150)->nullable()->comment('Hasan Yüksektepe A.Ş');
            $table->string('email')->nullable()->comment('ahmetdaldemir@gmail.com');
            $table->text('address')->nullable();
            $table->string('postal_code', 10)->default(34100);
            $table->integer('city')->nullable();
            $table->integer('district')->nullable();
            $table->string('country', 20)->nullable();
            $table->string('country_code', 3)->default('TR');
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
        Schema::dropIfExists('companies');
    }
};
