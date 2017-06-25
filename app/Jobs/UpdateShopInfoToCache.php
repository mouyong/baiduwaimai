<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateShopInfoToCache implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var int $$shop_id  baidu_shops 表中的 id */
    private $shop_id;
    private $baidu;

    public function __construct($shop_id)
    {
        $this->shop_id = $shop_id;
    }

    public function handle()
    {
        // 调用接口，查出 shop 在易联云的所有信息
        $shopInfo = self::shopInfo();

        // 查询不到信息
        if ($shopInfo['status'] != 0) {
            throw new \LogicException('找不到对应的记录进行更新：baidu_shops id:' . $this->shop_id);
        }

        $this->baidu->shop_id = $shopInfo['data']['baidu_shop_id'];

        \Cache::forget('bdwm:shop:' . $this->baidu->shop_id);
        $this->baidu->shopInfoFromCache($shopInfo['data']);
    }

    protected function shopInfo()
    {
        $this->baidu = app('baidu');
        return $this->baidu->send(['id' => $this->shop_id], bdwm_info_url());
    }
}
