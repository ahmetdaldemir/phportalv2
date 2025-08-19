<?php

use App\Models\Company;
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
        Schema::create('sellers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id');
            $table->string('name');
            $table->string('phone')->nullable();
            $table->boolean('is_status')->default(1);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->boolean('can_see_stock')->default(1);
            $table->boolean('can_see_cost_price')->default(1);
            $table->boolean('can_see_base_cost_price')->default(1);
            $table->boolean('can_see_sale_price')->default(1);
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sellers');
    }
};
