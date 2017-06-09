<?php

namespace App\Traits;

use Illuminate\Support\Facades\Input;

trait Order
{
    use Baidu;

    protected $ticket;

    /**
     * 初始化相关变量
     */
    public function __construct()
    {
        $this->set();
        $this->ticket = Input::get('ticket');
    }

    /**
     * 确认订单
     *
     * @param string $ticket
     * @param string $order_id
     * @return void
     */
    public function confirm(string $order_id)
    {
        $params = $this->buildCmd('order.confirm', $this->ticket, compact('order_id'));
        $this->res = $this->zttp->post(bd_api_url(), $params);
    }

    /**
     * 取消订单
     *
     * @param string $reason
     * @param string|integer $type
     * @return void
     */
    public function cancel(string $reason = '测试', $type = "-1")
    {
        $body['order_id'] = Input::get('order_id');
        $body['type'] = $type;
        $body['reason '] = $reason;

        $this->res = $this->zttp->post(
            bd_api_url(),
            $this->buildCmd('order.cancel', $this->ticket, $body)
        );
    }

    /**
     * 完成订单
     */
    public function complete()
    {
        $order_id = Input::get('order_id');

        $this->res = $this->zttp->post(
            bd_api_url(),
            $this->buildCmd('order.complete', $this->ticket, compact('order_id'))
        );
    }

    /**
     * 获取订单状态
     */
    public function status()
    {
        $cmd = Input::get('cmd');
        $order_id = Input::get('order_id');

        if ($cmd === 'order.status.push') {
            $body = Input::get('body');
            \Log::info($body);

            $data['errno']=0;
            $data['error']='success';

            $args = $this->buildCmd('resp.order.status.push', $this->ticket, [], 0);
        } elseif($cmd === 'order.status.get') {
            $args = $this->buildCmd('order.status.get', $this->ticket, compact('order_id'));
        }

        $this->res = $this->zttp->post(bd_api_url(), $args);
    }

    /**
     * 获取订单详情
     *
     * @param string $order_id
     * @return mixed
     */
    public function detail(string $order_id = '14970121408737')
    {
        $args = $this->buildCmd('order.get', $this->ticket, compact('order_id'));
        $res = $this->zttp->post(bd_api_url(), $args);
        \Log::info($res->json());
        return $res->json();
    }

    /**
     * 订单打印
     *
     * @param array $detail
     * @return mixed
     */
    public function print(array $detail)
    {
        // code
    }
}