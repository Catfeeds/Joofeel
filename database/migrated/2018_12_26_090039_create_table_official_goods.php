<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableOfficialGoods extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_enter')->create('official_goods', function (Blueprint $t) {
            $t->increments('id');
            $t->string('thu_url',50);
            $t->string('title',20);
            $t->decimal('price',8,2);
            $t->string('url',50);
            $t->date('end_time');
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
        Schema::dropIfExists('official_goods');

    }
}
