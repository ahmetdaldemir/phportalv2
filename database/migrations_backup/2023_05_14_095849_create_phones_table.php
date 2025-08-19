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
        Schema::create('phones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('brand_id');
            $table->unsignedBigInteger('version_id');
            $table->unsignedBigInteger('color_id');
            $table->unsignedBigInteger('seller_id');
            $table->integer('quantity')->default(0);
            $table->string('type')->nullable();
            $table->string('imei', 15)->nullable();
            $table->string('barcode')->nullable();
            $table->text('description')->nullable();
            $table->double('cost_price')->default(0);
            $table->double('sale_price')->default(0);
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->text('altered_parts')->nullable();
            $table->text('physical_condition')->nullable();
            $table->string('memory')->nullable();
            $table->string('batery')->nullable();
            $table->string('warranty')->nullable();
            $table->boolean('status')->default(1);
            $table->integer('invoice_id')->nullable();
            $table->boolean('is_confirm')->default(0);
            $table->string('sales_person')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');
            $table->foreign('version_id')->references('id')->on('versions')->onDelete('cascade');
            $table->foreign('color_id')->references('id')->on('colors')->onDelete('cascade');
            $table->foreign('seller_id')->references('id')->on('sellers')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('invoice_id')->references('id')->on('invoices');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('phones');
    }
};
