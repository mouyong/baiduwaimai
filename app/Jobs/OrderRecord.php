<?php

namespace App\Jobs;

use App\Models\Record;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class OrderRecord implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $order_id;
    public $content;
    public $shopInfo;

    /**
     * OrderRecord constructor.
     *
     * @param string $order_id
     * @param string $content
     * @param array $shopInfo
     */
    public function __construct($order_id, $content, array $shopInfo)
    {
        $this->order_id = $order_id;
        $this->content = $content;
        $this->shopInfo = $shopInfo;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data['order_id'] = $this->order_id;
        $data['content'] = $this->content;
        $data['baidu_shop_id'] = $this->shopInfo['baidu_shop_id'];
        $data['yilianyun_user_id'] = $this->shopInfo['user_id'];
        $data['yilianyun_api_key'] = $this->shopInfo['api_key'];
        $data['machines'] = json_encode($this->shopInfo['machines']);
        $data['fonts_setting'] = json_encode($this->shopInfo['fonts_setting']);
        $data['raw'] = json_encode($this->shopInfo);

        Record::create($data);
    }
}
