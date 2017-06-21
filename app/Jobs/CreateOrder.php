<?php

namespace App\Jobs;

use App\Models\Record;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CreateOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $order_id;
    private $content;
    private $shopInfo;

    public function __construct($order_id, $content, $shopInfo)
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
        $data['baidu_source'] = $this->shopInfo['baidu_source'];
        $data['baidu_secret_key'] = $this->shopInfo['baidu_secret_key'];
        $data['yilianyun_user_id'] = $this->shopInfo['user_id'];
        $data['yilianyun_api_key'] = $this->shopInfo['api_key'];
        $data['yilianyun_api_key'] = $this->shopInfo['api_key'];
        $data['machines'] = json_encode($this->shopInfo['machines']);
        $data['fonts_setting'] = json_encode($this->shopInfo['fonts_setting']);
        $data['raw'] = json_encode($this->shopInfo);

        Record::create($data);
    }
}
