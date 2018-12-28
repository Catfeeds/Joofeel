<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangePartyGoods extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('party_goods',function (Blueprint $t){
            $t->tinyInteger('type')->default(\App\Models\MiniProgram\Party\PartyGoods::GOODS);
            $t->tinyInteger('sign')->default(\App\Models\MiniProgram\Party\PartyGoods::BUY);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
