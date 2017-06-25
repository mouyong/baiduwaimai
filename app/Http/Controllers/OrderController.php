<?php

namespace App\Http\Controllers;

use App\Jobs\ConfirmOrder;
use Baidu\Baidu;
use Illuminate\Support\Facades\Input;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

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
        $input = Input::all();
        // 获取数据
        $body = json_decode($input['body'], true);

        // 从缓存获取订单详情
        $detail = $this->baidu->detailFromCache($body['order_id']);

        $this->baidu->shop_id = $detail['data']['shop']['baidu_shop_id'];
        $shop = $this->baidu->shopInfoFromCache(
            $this->baidu->shop_id
        );

        // 系统中不存在绑定该开放平台的用户
        if (is_null($shop)) {
            \Cache::forget('bdwm:shop:' . $this->baidu->shop_id);
            throw new UnauthorizedHttpException('系统中不存在绑定该开放平台的用户');
        }

        // 自动接单
        if ($shop['order_auto_confirm'] == 'yes') {
            $this->dispatch((new ConfirmOrder(
                $detail['data']['order']['order_id']
            ))->onQueue('create'));
        }

        $source_order_id = uuid();
        return $this->baidu->buildRes('resp.order.create', compact('source_order_id'), 0);
    }

    public function __call($method, $paramterment)
    {
        return $this->baidu->{$method}(...$paramterment);
    }
}
