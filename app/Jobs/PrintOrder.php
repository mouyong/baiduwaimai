<?php

namespace App\Jobs;

use App\Traits\Printer;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PrintOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use Printer;

    public $shopInfo;
    public $content;
    public $key;
    public $order_id;

    /**
     * Create a new job instance.
     *
     * @param array $shopInfo
     * @param string $content
     * @param int $key 当前执行打印的终端是 machine 中的 第几个终端，需要根据这个取出终端的 mkey，msing 等相关信息
     */
    public function __construct(array $shopInfo, $content = '', $key = 0, $order_id)
    {
        $this->shopInfo = $shopInfo;
        $this->content = $content;
        $this->key = $key;
        $this->order_id = $order_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // 生成此次打印的数据，包括但不限于 签名
        $data = gen_y_sign_and_data($this->content, $this->shopInfo, $this->key);

        // 调用打印接口，发送需要打印的数据
        Printer::printer($data);

        // 将此次打印的内容存入数据库
        dispatch((new OrderRecord($this->order_id, $this->content, $this->shopInfo))->onQueue('record'));
    }
}
