<?php

namespace App\Jobs;

use App\Traits\Shop;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateShopInfoToCache implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    use Shop;

    protected $shop_id;
    protected $source;

    public function __construct($shop_id)
    {
        $this->shop_id = $shop_id;
    }

    public function handle()
    {
        $shopInfo = self::shopInfo();
        $this->source = $shopInfo['baidu_source'];

        \Cache::forget('bdwm:' . $this->source);
        self::shopInfoFromCache($shopInfo);
    }

    protected function shopInfo()
    {
        $res = \Zttp\Zttp::asFormParams()
            ->post(bdwm_info_url(), ['shop_id' => $this->shop_id])
            ->json();

        return $res['data'];
    }
}
