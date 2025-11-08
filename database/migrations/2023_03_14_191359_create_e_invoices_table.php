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
        Schema::create('e_invoices', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');

            $table->string('invoiceType')->comment("SATIS");
            $table->dateTime('issueDate')->default(\Carbon\Carbon::today())->comment("2023-03-01T00:00:00");
            $table->string('elementId')->nullable()->comment("SD02023000000027");
            $table->float('invoiceTotal')->comment("32000.0");
            $table->string('supplierVknTckn',15)->comment("7550653667");
            $table->text('supplierPartyName')->comment("SAYICI DİJİTAL TEKNOLOJİ ÜRÜNLERİ İÇ VE DIŞ TİCARET LİMİTED ŞİRKETİ");
            $table->text('customerPartyName')->comment("ERK TELEKOM NAKLİYAT PETROL TİCARET LİMİTED ŞİRKETİ");
            $table->string('customerVknTckn')->comment("3600330874");
            $table->text('description')->nullable()->comment("Yazıyla Toplam Tutar: OtuzİkiBinTürkLirasıSıfırKuruş");
            $table->string('profileID')->comment("TICARIFATURA");
            $table->string('uuid')->comment("ddeaa0e0-27cc-43b0-a3b7-fb3f551e1d78");
            $table->string('currencyUnit',4)->comment("TRY");
            $table->float('taxAmount')->comment("4881.36");
            $table->float('payableAmount')->comment("32000.0");
            $table->float('allowanceTotalAmount')->comment("0.0");
            $table->float('taxInclusiveAmount')->comment("32000.0");
            $table->float('taxExclusiveAmount')->comment("27118.64");
            $table->float('lineExtensionAmount')->comment("27118.64");
            $table->string('pKAlias')->comment("urn:mail:defaultpk@erktelekomltdsti.com.tr");
            $table->string('gBAlias')->comment("urn:mail:defaultgb@sayicidijital.com.tr");
            $table->string('envelopeId')->nullable()->comment("C89D6229-A305-4569-BEB5-C3EDDE48A454");
            $table->dateTime('currentDate')->default(\Carbon\Carbon::today())->comment("0001-01-01T00:00:00");
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('e_invoices');
    }
};
