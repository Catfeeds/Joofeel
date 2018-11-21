<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableGoods extends Migration
{

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('goods');

        Schema::dropIfExists('goods_category');

        Schema::dropIfExists('goods_label');

        Schema::dropIfExists('recommend');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * 商品
         */
        Schema::create('goods',function (Blueprint $t){
            $t->engine = 'InnoDB';
            $t->increments('id');
            $t->string('goods_id',11)->index();       //商品编码
            $t->string('name',50);                    //商品名
            $t->tinyInteger('category_id')->index();         //分类id
            $t->integer('stock');                            //库存
            $t->integer('sold');                             //销售
            $t->string('notice');                            //须知
            $t->integer('carriage')->default(0);             //运费
            $t->string('recommend_reason');                  //推荐理由
            $t->string('channels');                          //购买渠道
            $t->string('purchase_address');                  //购买地址
            $t->string('shop');                              //商家名
            $t->string('delivery_place');                    //发货地址
            $t->integer('logistics_standard');               //物流标准
            $t->decimal('purchase_price',10,2);  //采购价
            $t->decimal('cost_price',10,2);      //成本价
            $t->decimal('reference_price',10,2); //参考价
            $t->decimal('price',10,2);           //定价
            $t->decimal('sale_price',10,2);      //折扣价
            $t->string('country',20);                 //国家
            $t->string('brand',20)->default('');                  //品牌
            $t->string('degrees',20)->default('');                 //度数
            $t->string('type',20)->default('');                   //种类
            $t->string('specifications',20)->default('');         //规格
            $t->string('flavor',20)->default('');                 //口味
            $t->string('thu_url',120);                //缩略图
            $t->string('cov_url',120);                //封面图
            $t->string('det_url',120);                //详情图
            $t->tinyInteger('isPending')->default(\App\Models\Goods\Goods::PENDING)->index();
            $t->tinyInteger('isShelves')->default(\App\Models\Goods\Goods::SHELVES)->index();           //是否上架
            $t->timestamps();
        });

        /**
         * 商品分类
         */
        Schema::create('goods_category',function (Blueprint $t){
            $t->increments('id');
            $t->string('name',4)->nullable();
        });

        /**
         * 商品标签
         */
        Schema::create('goods_label',function (Blueprint $t){
            $t->increments('id');
            $t->integer('goods_id')->nullable();
            $t->string('label_name',10)->nullable();
            $t->timestamps();
        });

        /**
         * 推荐商品
         */
        Schema::create('recommend',function (Blueprint $t){
            $t->increments('id');
            $t->integer('goods_id')->nullable();
            $t->timestamps();
        });
    }
}
