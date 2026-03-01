<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVascularsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vasculars', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('audit_id');
            $table->unsignedBigInteger('ivtype_id');
            $table->boolean('success')->default(1);
            $table->string('location')->nullable();
            $table->string('size')->nullable();
            $table->unsignedBigInteger('ivsite_id')->nullable();
            $table->timestamps();

            $table->foreign('audit_id')->references('id')
            ->on('audits')->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vasculars');
    }
}
