<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSwotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('swots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id');
            $table->text('strength');
            $table->text('weakness');
            $table->text('opportunity');
            $table->text('threat');
            $table->timestamps();

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
        Schema::dropIfExists('swots');
    }
}
