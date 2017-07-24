<?php

namespace App\Http\Controllers;

class BaiduController extends Controller
{
    public $baidu;

    public function __construct()
    {
        $this->baidu = app('baidu');
    }

    public function notify($id)
    {
        dispatch((new \App\Jobs\UpdateShopInfoToCache($id))->onQueue('update'));
        return ['errno' => 0, 'error' => 'success'];
    }

    /**
     * 登录百度进行绑定
     *
     * @param $shop_id
     * @return array|bool
     */
    public function authorized($shop_id)
    {
        $data = $this->baidu->authorized($shop_id);

        if (!$data) {
            return ['errno' => 202, 'error' => 'not found not upper limit source'];
        }
        return $data;
    }

    /**
     * 获取已绑定的百度商家详情
     *
     * @param string $shop_id
     * @return mixed
     */
    public function shop($shop_id, $source)
    {
        // 开启店铺订单推送
        $res = $this->baidu->openOrderPush($shop_id, $source);

        // 商家开启推单失败
        if ($res['body']['data'] != 1) {
            $response = [
                'errno' => 403,
                'error' => '您还未进项授权，请授权后再试',
            ];

            // 订单推送开启失败：
            $body = $res['body'];
            if ($body['errno'] == 20253) {
                $response['errno'] = $res['body']['errno']; // errno 20253
                $response['error'] = $res['body']['error']; // shop not exist
            }
            return response()->json($response, JSON_UNESCAPED_UNICODE);
        }

        // 获取百度响应的商家信息
        $response = $this->baidu->getShopInfo($shop_id);

        // 过滤商家信息，并进行响应
        return $this->baidu->getShopResponse($response);
    }
}
