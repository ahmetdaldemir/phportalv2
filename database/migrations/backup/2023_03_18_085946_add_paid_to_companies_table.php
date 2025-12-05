<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('web_sites',100)->nullable();
            $table->string('commercial_registration_number',50)->nullable()->comment('Ticari Sicil No');
            $table->string('tax_number',50)->nullable()->comment('Vergi No');
            $table->string('tax_office',150)->nullable()->comment('Vergi Dairesi');
            $table->string('mersis_number',50)->nullable()->comment('Mersis No');
            $table->string('company_name',150)->nullable()->comment('Hasan Yüksektepe A.Ş');
            $table->string('email',100)->nullable()->comment('ahmetdaldemir@gmail.com');
            $table->text('address')->nullable();
            $table->string('postal_code',10)->default('34100');
            $table->integer('city')->nullable();
            $table->integer('district')->nullable();
            $table->string('country',20)->nullable('Türkiye');
            $table->string('country_code',3)->default('TR');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('web_sites');
            $table->dropColumn('commercial_registration_number');
            $table->dropColumn('tax_number');
            $table->dropColumn('tax_office');
            $table->dropColumn('mersis_number');
            $table->dropColumn('company_name');
            $table->dropColumn('email');
            $table->dropColumn('address');
            $table->dropColumn('postal_code');
            $table->dropColumn('city');
            $table->dropColumn('district');
            $table->dropColumn('country');
            $table->dropColumn('country_code');
        });
    }
};
