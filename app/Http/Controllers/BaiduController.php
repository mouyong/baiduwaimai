<?php

namespace App\Http\Controllers;

class BaiduController extends Controller
{
    public $baidu;

    public function __construct()
    {
        $this->baidu = app('baidu');
    }

    /**
     * 获取已绑定的百度商家详情
     *
     * @param string $shop_id
     * @return mixed
     */
    public function shop($shop_id = null)
    {
        if (is_null($shop_id)) {
            return ['errno' => 401, 'error' => '缺少必要参数'];
        }

        // 开启店铺订单推送
        $res = $this->baidu->openOrderPush($shop_id);

        // 商家开启推单失败
        if ($res['body']['data'] != 1) {
            throw new \LogicException('订单推送开启失败：商户 id: ' . $shop_id);
        }

        // 获取百度响应的商家信息
        $response = $this->baidu->getShopInfo($shop_id);

        // 过滤商家信息，并进行响应
        return $this->baidu->getShopResponse($response);
    }
}
