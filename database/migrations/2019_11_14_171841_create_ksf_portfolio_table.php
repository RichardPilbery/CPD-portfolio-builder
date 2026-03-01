<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKsfPortfolioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ksf_portfolio', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('ksf_id');
            $table->unsignedBigInteger('portfolio_id');


            $table->foreign('ksf_id')->references('id')
            ->on('ksfs')->onDelete('cascade');
            $table->foreign('portfolio_id')->references('id')
            ->on('portfolios')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ksf_portfolio');
    }
}
