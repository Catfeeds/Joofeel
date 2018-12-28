<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCollection extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('collection',function (Blueprint $t){
            $t->increments('id');
            $t->integer('product_id')->index();
            $t->integer('user_id')->index();
            $t->tinyInteger('type')->default(\App\Models\MiniProgram\Collection::GOODS)->index();
            $t->tinyInteger('state')->default(\App\Models\MiniProgram\Collection::COLLECTION)->index();
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
        Schema::dropIfExists('collection');
    }
}
