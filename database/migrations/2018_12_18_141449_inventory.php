<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Inventory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //进销存系统
        Schema::create('inventory',function (Blueprint $t){
            $t->engine = 'InnoDB';
            $t->increments('id');
            $t->string('brand',10)->index();
            $t->string('goods_name',16)->index();
            $t->string('batch_no',10)->index();
            $t->decimal('purchase_price',8,2);
            $t->integer('put_count'); //入库数量
            $t->integer('in_count');  //在库数量
            $t->decimal('put_price',8,2); //入库金额
            $t->decimal('in_price',8,2);  //在库金额
            $t->date('in_day');    //入库日期
            $t->date('overdue_day');  //过期时间
        });

        /**
         * 出库
         */
        Schema::create('outbound',function (Blueprint $t){
            $t->engine = 'InnoDB';
            $t->increments('id');
            $t->integer('inventory_id')->index(); //库存商品id
            $t->integer('count'); //出库数量
            $t->decimal('out_price',8,2); //出库金额
            $t->string('executor',10); //执行人
            $t->date('out_date'); // 出库时间
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory');
        Schema::dropIfExists('outbound');
    }
}
