<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableChange extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('party',function (Blueprint $t){
            $t->tinyInteger('isCommunity')->index()->default(\App\Models\MiniProgram\Party\Party::NOT_COMMUNITY);
        });

        Schema::table('user',function (Blueprint $t){
            $t->tinyInteger('isTalent')->index()->default(\App\Models\MiniProgram\User\User::NOT_TALENT);
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
