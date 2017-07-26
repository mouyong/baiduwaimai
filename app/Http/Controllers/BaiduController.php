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
    public function authorized($shop_id, $source = null)
    {
        $data = $this->baidu->authorized($shop_id, $source);
        // ka 用户，但是当前 source 已经达到了 200 限制
        if (is_array($data) && empty($data)) {
            return ['errno' => 202, 'error' => '<span style="color: blue;">ka</span> not found not upper limit source'];
        }

        if (is_null($data)) {
            return ['errno' => 203, 'error' => '您的店铺正在审核中'];
        }

        if ($data === false) {
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
        if ($res['body']['errno'] != 0) {
            $response = [
                'errno' => 403,
                'error' => '<div style="text-align: left;padding-left: 2.5em;">百度外卖接口出现未知错误。<br>错误码：' . $res['body']['errno'] . '<br>错误消息提示：' .  $res['body']['error'] . '</div>', // 百度外卖接口出错。未知错误
            ];

            // 订单推送开启失败：
            if ($res['body']['errno'] == 20253) {
                $response['errno'] = $res['body']['errno']; // errno 20253
                $response['error'] = '未查找到该商户信息。你可能还未进行授权'; // shop not exist
                $response['info'] = $res['body']['error']; // shop not exist
            }
            return response()->json($response, JSON_UNESCAPED_UNICODE);
        }

        // 获取百度响应的商家信息
        $response = $this->baidu->getShopInfo($shop_id);

        // 过滤商家信息，并进行响应
        return $this->baidu->getShopResponse($response);
    }
}
