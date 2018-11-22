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
            $t->tinyInteger('isShow')
                ->default(\App\Models\Banner::SHOW)->index();
            $t->timestamps();
        });
    }
}
