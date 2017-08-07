<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RecordsAddForgenKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('records', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('user');
            $table->foreign('baidu_shop_id')->references('baidu_shop_id')->on('baidu_shops');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('records', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['baidu_shop_id']);
        });
    }
}
