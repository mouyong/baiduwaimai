<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\orderIdProvider;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OrderTest extends TestCase
{
    use OrderIdProvider;

    public $api;
    public $res;
    public $baidu;

    public function setUp()
    {
        parent::setUp();
        $this->api = 'api/order';
        $this->baidu = $this->app->make('baidu');
        $this->res = [
            'errno' => 0,
            'error' => 'success',
        ];
    }

    /**
     * @param array $data
     * @dataProvider createDataProvider
     */
    public function testCreate(array $data)
    {
        $response = $this->post($this->api, $data);
        $response->assertJsonFragment($this->res);
    }

    public function createDataProvider()
    {
        return [
            [
                'create' => [
                    'cmd' => 'order.create',
                    'body' => '{"order_id":"14993121639797"}',
                ]
            ]
        ];
    }

    /**
     * @param string $order_id
     * @dataProvider orderIdProvider
     */
    public function testConfirm($order_id)
    {
        $response = $this->baidu->confirm($order_id);
        $this->assertArraySubset(['body' => ['data'=>'']], $response);
    }

    /**
     * @param $order_id
     * @dataProvider orderIdProvider
     */
    public function testCancel($order_id)
    {
        $cmd = 'order.cancel';
        $response = $this->post($this->api, compact('cmd', 'order_id'));

        $response->assertJsonFragment(array_merge($this->res, ['data' => true]));
    }

    /**
     * @param $order_id
     * @dataProvider orderIdProvider
     */
    public function testComplete($order_id)
    {
        $cmd = 'order.complete';
        $response = $this->post($this->api, compact('cmd', 'order_id'));
        $res = [
            'errno' => 20216,
            'error' => '订单已取消',
            'data' => ''
        ];
        $response->assertJsonFragment($res);
    }

    /**
     * @param string $order_id
     * @dataProvider orderIdProvider
     */
    public function testStatusGet($order_id)
    {
        $cmd = 'order.status.get';
        $response = $this->post($this->api, compact('cmd', 'order_id'));
        $response->assertJsonFragment($this->res);
    }

//    /**
//     * 该部分代码，因未重构，运行效率差，耦合性高，暂时无法进行单元测试。需要通过 postman 人工进行测试。
//     * 人工进行测试时，需要注释 Printer 任务中的发送打印 Job 与数据库写入 Job。提高效率
//     *
//     * @param $order_id
//     * @param int $status
//     * @dataProvider orderIdProvider
//     */
//    public function testStatusPush($order_id, $status = 5)
//    {
//        $cmd = 'order.status.push';
//        $body = compact('order_id', 'status');
//        $response = $this->post($this->api, compact('cmd', 'body'));
//        $response->assertJsonFragment($this->res);
//    }
}
