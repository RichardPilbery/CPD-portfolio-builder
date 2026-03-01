<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email', 250)->unique();
            $table->string('pin')->nullable();
            $table->unsignedBigInteger('role_id')->default(3);
            $table->unsignedBigInteger('service_id')->default(17);
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('last_login')->useCurrent();
            $table->string('password');
            $table->integer('admin')->default(0);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
