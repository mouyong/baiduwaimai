<?php

namespace App\Http\Controllers;

use App\Jobs\ConfirmOrder;
use App\Models\Record;
use Baidu\Baidu;
use Illuminate\Support\Facades\Input;

class OrderController extends Controller
{
    private $baidu;

    public function __construct(Baidu $baidu)
    {
        $this->baidu = $baidu;
    }

    /**
     * @inheritdoc
     * @return mixed
     */
    public function order()
    {
        $source_order_id = uuid();

        $input = Input::all();

        $getDetail = first_no_null($input['getDetail']);

        $source = source($input['source']) ?: $input['source'];

        if (is_null($source)) {
            throw new \InvalidArgumentException("Unknow source");
        }

        $secret = secret_key($source);

        $secret = first_no_null($secret, first_no_null($input['secret']));

        // 获取数据
        $body = json_decode($input['body'], true);

        // 从缓存获取订单详情
        $detail = $this->baidu->detailFromCache($body['order_id'], $source, $secret);

        // 获取订单详情接口
        if ($getDetail) {
            return $detail;
        }

        $this->baidu->shop_id = $detail['data']['shop']['baidu_shop_id'];
        $shop = $this->baidu->shopInfoFromCache(
            $this->baidu->shop_id
        );

        $data['order_id'] = $detail['data']['order']['order_id'];
        $data['source'] = $source;

        if (!is_null($shop)) {
            // 自动接单
            if ($shop['order_auto_confirm'] == 'yes') {
                $this->dispatch((new ConfirmOrder(
                    $source,
                    $detail['data']['order']['order_id'],
                    $data
                ))->onQueue('confirm'));
            }
        } else {
            \Cache::forget('bdwm:shop:'. $this->baidu->shop_id);
        }

        $res = $this->baidu->setAuth($source)->buildRes('resp.order.create', compact('source_order_id'), 0);
        return $res;
    }

    public function reprint($orderId)
    {
        $order = Record::orderId($orderId)->first();
        $shop = $order->shop;

        // 订单不存在
        if (is_null($order)) {
            //
        }
        dd();
    }

    public function __call($method, $parameters)
    {
        return $this->baidu->{$method}(...$parameters);
    }
}
