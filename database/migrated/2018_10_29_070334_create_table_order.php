<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //商品订单
        Schema::create('goods_order',function (Blueprint $t){
            $t->engine = 'InnoDB';
            $t->increments('id');
            $t->integer('user_id')->index();
            $t->string('order_id',16)->index();
            $t->string('tracking_id')->default(0);
            $t->string('tracking_company')->default(0);
            $t->string('prepay_id',40)->nullable();
            $t->decimal('price',10,2);
            $t->decimal('sale_price',10,2);
            $t->integer('sale')->default(0);
            $t->integer('carriage')->default(0);
            $t->string('receipt_name',20);
            $t->string('receipt_address',255);
            $t->string('receipt_phone',11);
            $t->tinyInteger('isSign')->index()
                ->default(\App\Models\MiniProgram\Order\GoodsOrder::NOTDELIVERY);
            $t->tinyInteger('isDeleteUser')->index()
                ->default(\App\Models\MiniProgram\Order\GoodsOrder::NOT_DELETE);
            $t->tinyInteger('isPay')->index()
                ->default(\App\Models\MiniProgram\Order\GoodsOrder::UNPAID);
            $t->timestamps();
        });

        /**
         * 商品订单下的分支
         */
        Schema::create('order_id',function (Blueprint $t){
            $t->engine = 'InnoDB';
            $t->increments('id');
            $t->integer('order_id')->index();
            $t->integer('goods_id')->index();
            $t->integer('user_id')->index();
            $t->tinyInteger('isPay')->index()
                ->default(\App\Models\MiniProgram\Order\OrderId::UNPAID);
            $t->tinyInteger('isDeleteUser')->index()
                ->default(\App\Models\MiniProgram\Order\OrderId::NOT_DELETE);
            $t->tinyInteger('isSelect')->index()
                ->default(\App\Models\MiniProgram\Order\OrderId::NOT_SELECT);
            $t->integer('count')->nullable();
            $t->decimal('price',10,2)->nullable();
            $t->timestamps();
        });

        /**
         * 退款记录
         */
        Schema::create('refund_order',function (Blueprint $t){
            $t->engine = 'InnoDB';
            $t->increments('id');
            $t->integer('order_id')->index()->nullable();
            $t->string('refundNumber',32); //自己生成的退款订单号
            $t->string('refund_reason')->nullable();
            $t->string('refuse_reason')->nullable();
            $t->tinyInteger('isAgree')->index()
                ->default(\App\Models\MiniProgram\Order\RefundOrder::UNTREATED);
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
        Schema::dropIfExists('goods_order');

        Schema::dropIfExists('order_id');

        Schema::dropIfExists('refund_order');
    }
}
