<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('versions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('image')->nullable();
            $table->unsignedBigInteger('brand_id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('user_id');
            $table->boolean('is_status')->default(1);
            $table->boolean('technical')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('brand_id')->references('id')->on('brands');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('versions');
    }
};
