<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableParty extends Migration
{

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('party');

        Schema::dropIfExists('party_order');

        Schema::dropIfExists('message');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        /**
         * 派对
         */
        Schema::create('party',function (Blueprint $t){
            $t->engine = 'InnoDB';
            $t->increments('id');
            $t->integer('user_id')->index()->nullable();
            $t->string('image',120);
            $t->string('description',255);
            $t->text('details');
            $t->string('way',4);
            $t->tinyInteger('people_no');
            $t->tinyInteger('remaining_people_no');
            $t->string('date',10);
            $t->string('time',5);
            $t->string('site');
            $t->integer('start_time');
            $t->string('longitude');
            $t->string('latitude');
            $t->tinyInteger('isDeleteAdmin')->index()->default(\App\Models\MiniProgram\Party\Party::NOT_DELETE);
            $t->tinyInteger('isCommunity')->index()->default(\App\Models\MiniProgram\Party\Party::NOT_COMMUNITY);
            $t->tinyInteger('isDeleteUser')->index()->default(\App\Models\MiniProgram\Party\Party::DELETE);
            $t->tinyInteger('isClose')->index()->default(\App\Models\MiniProgram\Party\Party::NOT_CLOSE);
            $t->timestamps();
        });

        /**
         * 派对订单记录
         */
        Schema::create('party_order',function (Blueprint $t){
            $t->increments('id');
            $t->integer('user_id')->index();
            $t->integer('party_id')->index();
            $t->tinyInteger('isDeleteUser')->index()->default(\App\Models\MiniProgram\Party\PartyOrder::NOT_DELETE);
            $t->timestamps();
        });

        /**
         * 派对订单记录
         */
        Schema::create('party_goods',function (Blueprint $t){
            $t->increments('id');
            $t->integer('goods_id')->index();
            $t->integer('party_id')->index();
        });

        /**
         * 派对留言板记录
         */
        Schema::create('message',function (Blueprint $t){
            $t->increments('id');
            $t->integer('user_id')->index();
            $t->integer('party_id')->index();
            $t->string('content',200);
            $t->timestamps();
        });
    }
}
