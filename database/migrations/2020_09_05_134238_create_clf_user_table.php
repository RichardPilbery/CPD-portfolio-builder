<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClfUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clf_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('clf_id');
            $table->unsignedBigInteger('user_id');

            $table->foreign('clf_id')->references('id')
            ->on('clfs')->onDelete('cascade');
            $table->foreign('user_id')->references('id')
            ->on('users')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clf_user');
    }
}
