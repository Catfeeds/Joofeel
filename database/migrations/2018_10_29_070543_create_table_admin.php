<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAdmin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * 派对留言板记录
         */
        Schema::create('admin',function (Blueprint $t){
            $t->increments('id');
            $t->string('account',20)->unique();
            $t->string('password');
            $t->string('nickname');
            $t->string('avatar');
            $t->string('api_token',64)->unique();
            $t->rememberToken();
            $t->integer('scope')->default(16);
            $t->tinyInteger('isBaned')->default(0);
            $t->integer('login_time');
            $t->timestamps();
        });
    }
}
