<?php

namespace App\Traits;

trait Shop
{
    public function shopInfoFromCache($data = null, $expire = 1440)
    {
        return \Cache::remember('bdwm:' . $this->source, $expire, function () use ($data){
            if (is_array($data)) {
                return $data;
            }

            if (empty($data)) {
                throw new \InvalidArgumentException('缺少必要参数');
            }

            $res = \Zttp\Zttp::asFormParams()
                ->post(bdwm_info_url(), ['source' => $data])
                ->json();

            if ($res['status'] == 200) {
                return $res['data'];
            }
        });
    }
}