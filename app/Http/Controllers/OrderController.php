<?php

namespace App\Http\Controllers;

use App\Contracts\OrderInterface;
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
        // 获取数据
        $body = json_decode(Input::get('body'));
        $order_id = $body->order_id;

        // 确认订单
        $this->confirm($order_id);

        // todo debug 获取订单详情
        $detail = $this->detail($order_id);
        // todo 打印订单
//        $this->print($detail);

        // todo 数据库中的记录 id
        $source_order_id = uuid();
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
