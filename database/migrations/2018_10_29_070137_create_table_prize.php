<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePrize extends Migration
{

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prize');

        Schema::dropIfExists('prize_order');
    }


    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        /**
         * 奖品
         */
        Schema::create('prize',function (Blueprint $t){
            $t->increments('id');
            $t->integer('goods_id')->index();
            $t->integer('open_prize_time');
            $t->string('share_url')->nullable();
            $t->tinyInteger('isPrize')->unique()
                ->default(\App\Models\Prize\Prize::ONGOING); //标记是否处于抽奖中 0抽奖中1结束
            $t->timestamps();
        });

        /**
         * 抽奖记录
         */
        Schema::create('prize_order',function (Blueprint $t){
            $t->increments('id');
            $t->integer('user_id')->index();
            $t->integer('prize_id')->index();
            $t->string('form_id',50);
            $t->tinyInteger('isLucky')->default(\App\Models\Prize\PrizeOrder::NOT_LUCKY)->index(); //标记是否中奖 0未中奖1中奖
            $t->timestamps();
        });
    }
}
