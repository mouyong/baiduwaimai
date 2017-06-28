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
     * 登录百度进行绑定
     *
     * @param $shop_id
     */
    public function authorized($shop_id)
    {
        return $this->baidu->authorized($shop_id);
    }

    /**
     * 获取已绑定的百度商家详情
     *
     * @param string $shop_id
     * @return mixed
     */
    public function shop($shop_id)
    {
        // 开启店铺订单推送
        $res = $this->baidu->openOrderPush($shop_id);

        // 商家开启推单失败
        if ($res['body']['data'] != 1) {
            $message = '很可能还未进项授权，所以无法获取到用户的商店名称';
            throw new \LogicException('订单推送开启失败：商户 id: ' . $shop_id . '，'. $res['body']['error'] . $message);
        }

        // 获取百度响应的商家信息
        $response = $this->baidu->getShopInfo($shop_id);

        // 过滤商家信息，并进行响应
        return $this->baidu->getShopResponse($response);
    }
}
