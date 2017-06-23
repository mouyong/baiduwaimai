<?php

namespace App\Contracts;


interface OrderInterface
{
    /**
     * 订单接收，进行任务调度处理
     *
     * @return mixed
     */
    public function order();

    /**
     * 当接受到有订单后，进行订单确认
     *
     * @param string $order_id
     * @param string $ticket
     * @return mixed
     */
    public function confirm($order_id, $ticket);

    /**S
     * 订单取消
     *
     * @param string $reason 取消原因
     * @param string|integer $type 去向的类型
     * @return mixed
     */
    public function cancel($reason, $type = "-1");

    /**
     * 订单状态
     *
     * @return mixed
     */
    public function status();

    /**
     * 订单详情
     *
     * @return mixed
     */
    public function detail();
}
