<?php

namespace App\Http\Controllers;

use App\Contracts\OrderInterface;
use App\Jobs\ConfirmOrder;
use App\Traits\Order;
use Illuminate\Support\Facades\Input;

class OrderController extends Controller implements OrderInterface
{
    use Order;

    /**
     * @inheritdoc
     * @return mixed
     */
    public function order()
    {
        $this->source = Input::get('source');

        $shop = self::shopInfoFromCache($this->source);

        // 系统中不存在绑定该开放平台的用户
        if (is_null($shop)) {
            \Cache::forget('bdwm:' . $this->source);
        }

        $this->secret  = $shop['baidu_secret_key'];

        // TODO 订单打印流水号 记录数据库
        $source_order_id = uuid();

        // 获取数据
        $body = json_decode(Input::get('body'));

        // 不手动接单
        if ($shop['order_confirm'] != 'yes') {
            $this->dispatch(new ConfirmOrder($body->order_id, Input::all()));
        }

        return $this->buildRes('resp.order.create', $this->ticket, compact('source_order_id'), 0);
    }

    /**
     * TODO 调试而存在
     */
    public function __destruct()
    {
        if (isset($this->res)) {
            dd($this->res->json());
        }
    }
}
