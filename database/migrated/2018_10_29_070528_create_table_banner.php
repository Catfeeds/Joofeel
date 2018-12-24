<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableBanner extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('banner');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banner',function (Blueprint $t){
            $t->increments('id');
            $t->string('url');
            $t->tinyInteger('isPrize')->default(0);
            $t->string('image',100);
            $t->integer('goods_id');
            $t->tinyInteger('type');
            $t->tinyInteger('isShow')
                ->default(\App\Models\MiniProgram\Banner::SHOW)->index();
            $t->timestamps();
        });
    }
}
