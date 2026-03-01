<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAirwaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('airways', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('audit_id');
            $table->unsignedBigInteger('airwaytype_id');
            $table->boolean('success')->default(1);
            $table->unsignedSmallInteger('grade')->nullable();
            $table->decimal('size',2,1)->nullable();
            $table->boolean('bougie')->default(0);
            $table->unsignedBigInteger('capnography_id')->nullable();
            $table->text('notes')->nullable();
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
        Schema::dropIfExists('airways');
    }
}
