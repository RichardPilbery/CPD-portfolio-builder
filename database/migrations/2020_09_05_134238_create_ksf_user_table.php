<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKsfUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ksf_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ksf_id');
            $table->unsignedBigInteger('user_id');

            $table->foreign('ksf_id')->references('id')
            ->on('ksfs')->onDelete('cascade');
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
        Schema::dropIfExists('ksf_user');
    }
}
