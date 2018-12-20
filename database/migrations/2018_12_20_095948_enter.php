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
            $t->tinyInteger('is_ban',\App\Models\Enter\Merchants::NOT_BAN);
            $t->timestamps();
        });

        /**
         *推送
         */
        Schema::connection('mysql_enter')->create('push', function (Blueprint $t) {
            $t->increments('id');
            $t->string('title',20);   //标题
            $t->string('thu_url',30); //头图
            $t->text('content');             //内容
            $t->integer('merchants_id')->index();
            $t->integer('ticket_id')->index();
            $t->timestamps();
        });

        /**
         * 推送票
         */
        Schema::connection('mysql_enter')->create('ticket',function (Blueprint $t){
            $t->increments('id');
            $t->string('ticket_name',30);
            $t->integer('merchants_id')->index();
            $t->string('thu_url',30);
            $t->string('address',50);
            $t->string('lat',20);
            $t->string('lng',20);
            $t->decimal('price',8,2);
            $t->integer('stock');
            $t->date('start_time');
            $t->date('end_time');
            $t->tinyInteger('isSold',\App\Models\Enter\Ticket::SOLD);
        });

        /**
         * 推送票订单
         */
        Schema::connection('mysql_enter')->create('ticket_order',function (Blueprint $t){
            $t->increments('id');
            $t->string('order_id',20)->index();
            $t->integer('ticket_id')->index();
            $t->integer('user_id')->index();
            $t->string('prepay_id',40)->default(0);
            $t->decimal('price',8,2);
            $t->integer('count');
            $t->decimal('total_price',8,2);
            $t->tinyInteger('isUsed',\App\Models\Enter\Ticket::NOT_USE);
            $t->timestamps();
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

        Schema::dropIfExists('ticket');

        Schema::dropIfExists('ticket_order');
    }
}
