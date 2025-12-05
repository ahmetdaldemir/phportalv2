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
        Schema::create('e_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('company_id');
            $table->string('invoiceType', 255)->comment('SATIS');
            $table->dateTime('issueDate')->comment('2023-03-01T00:00:00');
            $table->string('elementId', 255)->nullable()->comment('SD02023000000027');
            $table->double('invoiceTotal');
            $table->string('supplierVknTckn', 15)->comment('7550653667');
            $table->text('supplierPartyName')->comment('SAYICI DİJİTAL TEKNOLOJİ ÜRÜNLERİ İÇ VE DIŞ TİCARET LİMİTED ŞİRKETİ');
            $table->text('customerPartyName')->comment('ERK TELEKOM NAKLİYAT PETROL TİCARET LİMİTED ŞİRKETİ');
            $table->string('customerVknTckn', 255)->comment('3600330874');
            $table->text('description')->nullable()->comment('Yazıyla Toplam Tutar: OtuzİkiBinTürkLirasıSıfırKuruş');
            $table->string('profileID', 255)->comment('TICARIFATURA');
            $table->string('uuid', 255)->comment('ddeaa0e0-27cc-43b0-a3b7-fb3f551e1d78');
            $table->string('currencyUnit', 4)->comment('TRY');
            $table->double('taxAmount');
            $table->double('payableAmount');
            $table->double('allowanceTotalAmount');
            $table->double('taxInclusiveAmount');
            $table->double('taxExclusiveAmount');
            $table->double('lineExtensionAmount');
            $table->string('pKAlias', 255)->nullable()->comment('urn:mail:defaultpk@erktelekomltdsti.com.tr');
            $table->string('gBAlias', 255)->nullable()->comment('urn:mail:defaultgb@sayicidijital.com.tr');
            $table->string('envelopeId', 255)->nullable()->comment('C89D6229-A305-4569-BEB5-C3EDDE48A454');
            $table->string('currentDate', 255)->comment('0001-01-01T00:00:00');
            $table->string('saveType');
            $table->string('invoiceStatus', 255)->default('Accept');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('e_invoices');
    }
};
