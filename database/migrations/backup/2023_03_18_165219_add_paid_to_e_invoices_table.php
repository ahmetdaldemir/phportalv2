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
        Schema::table('e_invoices', function (Blueprint $table) {
            $table->string('invoiceStatus')->default('Accept');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('e_invoices', function (Blueprint $table) {
            $table->dropColumn('invoiceStatus');
        });
    }
};
