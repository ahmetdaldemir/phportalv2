<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('e_invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('company_id');
            $table->string('invoiceType')->comment('SATIS');
            $table->datetime('issueDate')->comment('2023-03-01T00:00:00');
            $table->string('elementId')->comment('SD02023000000027');
            $table->decimal('invoiceTotal', 8, 2)->comment('32000.0');
            $table->string('supplierVknTckn', 15)->comment('7550653667');
            $table->text('supplierPartyName')->comment('SAYICI DİJİTAL TEKNOLOJİ ÜRÜNLERİ İÇ VE DIŞ TİCARET LİMİTED Ş...');
            $table->text('customerPartyName')->comment('ERK TELEKOM NAKLİYAT PETROL TİCARET LİMİTED ŞİRKETİ');
            $table->string('customerVknTckn')->comment('3600330874');
            $table->text('description')->comment('Yazıyla Toplam Tutar: OtuzİkiBinTürkLirasıSıfırKuruş');
            $table->string('profileID')->comment('TICARIFATURA');
            $table->string('uuid')->comment('ddeaa0e0-27cc-43b0-a3b7-fb3f551e1d78');
            $table->string('currencyUnit', 4)->comment('TRY');
            $table->decimal('taxAmount', 8, 2)->comment('4881.36');
            $table->decimal('payableAmount', 8, 2)->comment('32000.0');
            $table->decimal('allowanceTotalAmount', 8, 2)->comment('0.0');
            $table->decimal('taxInclusiveAmount', 8, 2)->comment('32000.0');
            $table->decimal('taxExclusiveAmount', 8, 2)->comment('27118.64');
            $table->decimal('lineExtensionAmount', 8, 2)->comment('27118.64');
            $table->string('pKAlias')->comment('urn:mail:defaultpk@erktelekomltdsti.com.tr');
            $table->string('gBAlias')->comment('urn:mail:defaultgb@sayicidijital.com.tr');
            $table->string('envelopeId')->comment('C89D6229-A305-4569-BEB5-C3EDDE48A454');
            $table->string('currentDate')->comment('0001-01-01T00:00:00');
            $table->enum('saveType', ['out', 'in']);
            $table->string('invoiceStatus')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('company_id')->references('id')->on('companies');
        });
    }

    public function down()
    {
        Schema::dropIfExists('e_invoices');
    }
};
