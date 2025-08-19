<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('seller_id')->nullable();
            $table->boolean('is_status')->default(1);
            $table->enum('position', ['1', '2'])->nullable();
            $table->boolean('personel')->default(0);
            $table->decimal('salary', 10, 2)->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('seller_id')->references('id')->on('sellers');
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};
