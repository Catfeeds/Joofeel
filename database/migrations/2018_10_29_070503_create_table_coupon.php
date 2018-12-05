<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCoupon extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * 优惠券
         */
        Schema::create('coupon',function (Blueprint $t){
            $t->engine = 'InnoDB';
            $t->increments('id');
            $t->string('name',10);
            $t->integer('rule')->default(0);
            $t->integer('sale');
            $t->tinyInteger('species');  //种类 1不固定时间(过期时间顺延) 2固定时间
            $t->tinyInteger('category');
            $t->integer('day');
            $t->integer('count');
            $t->integer('start_time');
            $t->integer('end_time');
            $t->tinyInteger('isReceive')
                ->default(\App\Models\Coupon\Coupon::CAN_RECEIVE)->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coupon');
    }
}
