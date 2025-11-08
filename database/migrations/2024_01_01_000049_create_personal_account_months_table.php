<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('personal_account_months', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('mounth');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('staff_id');
            $table->decimal('salary', 10, 2);
            $table->decimal('overtime', 10, 2);
            $table->decimal('way', 10, 2);
            $table->decimal('meal', 10, 2);
            $table->decimal('bounty', 10, 2);
            $table->decimal('insurance', 10, 2);
            $table->timestamps();
            
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('staff_id')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('personal_account_months');
    }
};
