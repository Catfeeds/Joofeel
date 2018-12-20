<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Enter extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_enter')->create('merchants', function (Blueprint $t) {
            $t->increments('id');
            $t->string('account',10)->unique();
            $t->string('password',15);
            $t->bigInteger('phone')->unique();
            $t->string('api_token',30)->index();
            $t->string('merchants_name',50);
            $t->timestamps();
        });

        /**
         *推送
         */
        Schema::connection('mysql_enter')->create('push', function (Blueprint $t) {
            $t->increments('id');
            $t->string('title',20);  //标题
            $t->text('content');            //内容
            $t->integer('merchants_id')->index();
            $t->integer('product_id')->index();
            $t->timestamps();
        });

        /**
         * 推送票
         */
        Schema::connection('mysql_enter')->create('ticket',function (Blueprint $t){
            $t->increments('id');
            $t->string('ticket_name',30);
            $t->string('thu_url',30);
            $t->string('address',50);
            $t->string('lat',20);
            $t->string('lng',20);
            $t->decimal('price',8,2);
            $t->integer('stock');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merchants');

        Schema::dropIfExists('push');
    }
}
