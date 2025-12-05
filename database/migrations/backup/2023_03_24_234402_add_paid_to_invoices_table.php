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
        Schema::table('invoices', function (Blueprint $table) {

            $table->double('exchange',10,2);
            $table->double('tax',10,2);
            $table->string('file');
            $table->enum('paymentStatus',['unpaid','paid','paidOutOfPocket'])->default('paid')->comment('paidOutOfPocket=>Personel Cebinden Ödedi');
            $table->date('paymentDate')->default(\Carbon\Carbon::today());
            $table->string('paymentStaff')->nullable();
            $table->string('periodMounth',10)->nullable();
            $table->year('periodYear')->nullable();

            $table->unsignedBigInteger('accounting_category_id');
            $table->foreign('accounting_category_id')->references('id')->on('accounting_categories')->onDelete('cascade');

            $table->double('currency')->comment('Vergiler Dahil');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            //
        });
    }
};
