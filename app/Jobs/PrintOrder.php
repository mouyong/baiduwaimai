<?php

namespace App\Jobs;

use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PrintOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $shopInfo;
    public $content;
    public $key;
    public $source;

    /**
     * Create a new job instance.
     *
     * @param array $shopInfo
     * @param string $content
     * @param int $key 当前执行打印的终端是 machine 中的 第几个终端，需要根据这个取出终端的 mkey，msing 等相关信息
     */
    public function __construct(array $shopInfo, $content = '', $key = 0)
    {
        $this->shopInfo = $shopInfo;
        $this->content = $content;
        $this->key = $key;
    }

    /**
     * Execute the job.
     *
     * @return bool
     */
    public function handle()
    {
        $client = new Client;

        // 生成此次打印的数据，包括但不限于 签名
        $data = gen_y_sign_and_data($this->content, $this->shopInfo, $this->key);
        $query = http_build_query($data);

        info($query);

        // 调用打印接口，发送需要打印的数据
        if (\App::environment('production')) {
        // if (\App::environment('production', 'local')) {
            $client->request('POST', y_api_url(), ['body' => $query]);
        }
        return true;
    }
}
