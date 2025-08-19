<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('authorized')->nullable();
            $table->string('web_sites', 100)->nullable();
            $table->string('commercial_registration_number', 50)->nullable()->comment('Ticari Sicil No');
            $table->string('tax_number', 50)->nullable()->comment('Vergi No');
            $table->string('tax_office', 150)->nullable()->comment('Vergi Dairesi');
            $table->string('mersis_number', 50)->nullable()->comment('Mersis No');
            $table->string('company_name', 150)->nullable()->comment('Şirket Ünvanı');
            $table->string('email', 100)->nullable();
            $table->text('address')->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->integer('city')->nullable();
            $table->integer('district')->nullable();
            $table->string('country', 20)->nullable();
            $table->string('country_code', 3)->nullable();
            $table->boolean('is_status')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('companies');
    }
};
