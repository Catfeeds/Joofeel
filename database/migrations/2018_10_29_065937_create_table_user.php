<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUser extends Migration
{


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user');

        Schema::dropIfExists('user_coupon');

        Schema::dropIfExists('delivery_address');

        Schema::dropIfExists('shopping_cart');

    }


    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        /**
         * 用户表
         */
        Schema::create('user', function (Blueprint $t) {
            $t->increments('id');
            $t->string('openid',30)->unique();
            $t->string('nickname')->nullable();
            $t->string('avatar')->nullable();
            $t->tinyInteger('isNewUser')
                ->default(\App\Models\MiniProgram\User\User::IS_NEW); //标记是否为新用户 0为新用户1为老用户
            $t->timestamps();
        });

        /**
         * 用户优惠券表
         */
        Schema::create('user_coupon', function (Blueprint $t) {
            $t->increments('id');
            $t->integer('user_id')->index();
            $t->integer('coupon_id')->index();
            $t->tinyInteger('status')
                ->default(\App\Models\MiniProgram\User\UserCoupon::NOT_USE)->index(); //标记该优惠券是否已使用 0未使用1使用
            $t->tinyInteger('state')
                ->default(\App\Models\MiniProgram\User\UserCoupon::CAN_USE)->index(); //标记该优惠券是否可以使用 0为可以使用1不可以使用
            $t->integer('start_time');
            $t->integer('end_time');
        });

        /**
         * 用户收货地址表
         */
        Schema::create('delivery_address',function (Blueprint $t){
            $t->increments('id');
            $t->integer('user_id')->index();
            $t->string('receipt_name',20);
            $t->string('receipt_area',20);
            $t->string('receipt_address',255);
            $t->string('receipt_phone',12);
            $t->tinyInteger('label')->default(\App\Models\MiniProgram\User\DeliveryAddress::HOME); //所属标签
            $t->tinyInteger('isDefault')->index();    //标记是否为默认地址 0是1不是
        });

        /**
         * 用户购物车表
         */
        Schema::create('shopping_cart',function (Blueprint $t){
            $t->increments('id');
            $t->integer('user_id')->index();
            $t->integer('goods_id')->index();
            $t->integer('count');
            $t->tinyInteger('isSelect')->default(\App\Models\MiniProgram\User\ShoppingCart::SELECTED); // 标记该条记录是否处于勾选中
            $t->timestamps();
        });
    }
}
