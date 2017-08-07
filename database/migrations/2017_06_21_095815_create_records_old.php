<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecordsOld extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    // public function up()
    // {
        // Schema::create('records', function (Blueprint $table) {
        //     $table->increments('id');

        //     $table->string('order_id', 36)->comment('百度订单 id');
        //     $table->longText('content')->comment('商户打印的内容');

        //     $table->string('baidu_shop_id', 20);
        //     $table->integer('yilianyun_user_id')->comment('易联云用户 id');
        //     $table->string('yilianyun_api_key', 40)->comment('易联云用户 api_key');
        //     $table->text('machines')->comment('易联云用户在用户确认订单时所拥有的打印终端');
        //     $table->text('fonts_setting')->comment('易联云用户在用户确认订单时使用的字体格式');

        //     $table->text('raw')->comment('易联云用户打印时未经过任何处理的的 百度 shop 信息');

        //     $table->timestamps();
        // });
    // }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    // public function down()
    // {
        // Schema::dropIfExists('records');
    // }
}
