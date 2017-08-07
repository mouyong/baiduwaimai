<?php

namespace Tests\Feature;

use App\Jobs\ConfirmOrder;
use App\Jobs\OrderRecord;
use App\Jobs\PrintOrder;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\orderIdProvider;

class JobTest extends TestCase
{
    use OrderIdProvider;

    public $shopInfo;
    public $content;
    public $query;

    public function setUp()
    {
        parent::setUp();
        $this->shopInfo = $this->shopInfo();
        $this->content = $this->content();
        $this->query = $this->query();
    }

    /**
     * @param string $order_id
     * @param string $queue
     * @dataProvider orderIdProvider
     */
    public function testConfirmPushed($order_id, $queue = 'confirm')
    {
        Queue::fake();
        dispatch((new ConfirmOrder($order_id))->onQueue($queue));
        Queue::assertPushed(ConfirmOrder::class, function ($job) use ($order_id) {
            return $job->order_id === $order_id;
        });
        Queue::assertPushedOn($queue, ConfirmOrder::class);
    }

    /**
     * @param string $order_id
     * @param string $queue
     * @param int $key
     * @dataProvider orderIdProvider
     */
    public function testPrinterPushed($order_id, $queue = 'print', $key = 0)
    {
        Queue::fake();
        dispatch((new PrintOrder($this->shopInfo(), $this->content(), $key, $order_id))->onQueue($queue));
        Queue::assertPushed(PrintOrder::class, function ($job) use ($order_id) {
            return $job->order_id === $order_id;
        });
        Queue::assertPushedOn($queue, PrintOrder::class);
    }

    /**
     * @param string $order_id
     * @param string $queue
     * @dataProvider orderIdProvider
     */
    public function testOrderRecord($order_id, $queue = 'record')
    {
        Queue::fake();
        dispatch((new OrderRecord($order_id, $this->content(), $this->shopInfo()))->onQueue($queue));
        Queue::assertPushed(OrderRecord::class, function ($job) use ($order_id) {
            return $job->order_id === $order_id;
        });
        Queue::assertPushedOn($queue, OrderRecord::class);
    }

    public function shopInfo()
    {
        return array(
            'id' => '186',
            'user_id' => '626',
            'baidu_shop_id' => '1717041709',
            'order_auto_confirm' => 'yes',
            'api_key' => '8c61ff8e4d1b6ed9930f6cb21029f67df630f92a',
            'machines' =>
                array(
                    0 =>
                        array(
                            'id' => '79987',
                            'mkey' => '1400450905',
                            'msign' => 'enck2sfnujen',
                            'version' => 0,
                        ),
                ),
            'fonts_setting' =>
                array(
                    'receive_info_size' => '2',
                    'receive_address_size' => '1',
                    'order_size' => '1',
                    'create_order_size' => '1',
                    'remark_size' => '2',
                    'product_size' => '2',
                    'mn' => '2',
                    'ad' => '3',
                    'shop_ad_content' => '《地方撒》alert&lt;?php echo $a;?&gt;
&lt;p&gt;this -&amp;gt; &amp;quot;&lt;/p&gt;',
                    'default' => '2',
                ),
        );
    }

    public function content()
    {
        return '@@2          **#1 百度 **\n\r................................\r@@2        ----货到付款----\n\r@@2            测试营销2\n\r下单时间:2017年07月06日11时36分
订单编号:14993121639797
**************商品**************          ---1号口袋---\n@@2同步菜            x1       10.00\n@@2Dan               x1        7.80\n\r--------------------------------\r配送费:5.00\r餐盒费:4.00\r********************************\r@@2订单总价:￥26.80
花样年·福年广场 1
@@2牟(先生): 186-9834-9096
@@2订单备注：[用餐人数]1人；不吃辣,@@2少放盐
@@2纳税人识别号：.1225779624
@@2发票抬头：发票抬头信息，是公司名@@2称什么的
@@2商家留言：《地方撒》alert&lt;?ph@@2p echo $a;?&gt;
&lt;p&gt;this -@@2&amp;gt; &amp;quot;&lt;/p&gt;
@@2            ** 完 **\n ';
    }

    public function query()
    {
        return 'machine_code=1400450905&partner=626&time=1500219177&sign=9795B8CB98301C1134DC537E47BDF353&content=%40%402++++++++++%2A%2A%231+%E7%99%BE%E5%BA%A6+%2A%2A%5Cn%5Cr................................%5Cr%40%402++++++++----%E8%B4%A7%E5%88%B0%E4%BB%98%E6%AC%BE----%5Cn%5Cr%40%402++++++++++++%E6%B5%8B%E8%AF%95%E8%90%A5%E9%94%802%5Cn%5Cr%E4%B8%8B%E5%8D%95%E6%97%B6%E9%97%B4%3A2017%E5%B9%B407%E6%9C%8806%E6%97%A511%E6%97%B636%E5%88%86%0A%E8%AE%A2%E5%8D%95%E7%BC%96%E5%8F%B7%3A14993121639797%0A%2A%2A%2A%2A%2A%2A%2A%2A%2A%2A%2A%2A%2A%2A%E5%95%86%E5%93%81%2A%2A%2A%2A%2A%2A%2A%2A%2A%2A%2A%2A%2A%2A++++++++++---1%E5%8F%B7%E5%8F%A3%E8%A2%8B---%5Cn%40%402%E5%90%8C%E6%AD%A5%E8%8F%9C++++++++++++x1+++++++10.00%5Cn%40%402Dan+++++++++++++++x1++++++++7.80%5Cn%5Cr--------------------------------%5Cr%E9%85%8D%E9%80%81%E8%B4%B9%3A5.00%5Cr%E9%A4%90%E7%9B%92%E8%B4%B9%3A4.00%5Cr%2A%2A%2A%2A%2A%2A%2A%2A%2A%2A%2A%2A%2A%2A%2A%2A%2A%2A%2A%2A%2A%2A%2A%2A%2A%2A%2A%2A%2A%2A%2A%2A%5Cr%40%402%E8%AE%A2%E5%8D%95%E6%80%BB%E4%BB%B7%3A%EF%BF%A526.80%0A%E8%8A%B1%E6%A0%B7%E5%B9%B4%C2%B7%E7%A6%8F%E5%B9%B4%E5%B9%BF%E5%9C%BA+1%0A%40%402%E7%89%9F%28%E5%85%88%E7%94%9F%29%3A+186-9834-9096%0A%40%402%E8%AE%A2%E5%8D%95%E5%A4%87%E6%B3%A8%EF%BC%9A%5B%E7%94%A8%E9%A4%90%E4%BA%BA%E6%95%B0%5D1%E4%BA%BA%EF%BC%9B%E4%B8%8D%E5%90%83%E8%BE%A3%2C%40%402%E5%B0%91%E6%94%BE%E7%9B%90%0A%40%402%E7%BA%B3%E7%A8%8E%E4%BA%BA%E8%AF%86%E5%88%AB%E5%8F%B7%EF%BC%9A.1225779624%0A%40%402%E5%8F%91%E7%A5%A8%E6%8A%AC%E5%A4%B4%EF%BC%9A%E5%8F%91%E7%A5%A8%E6%8A%AC%E5%A4%B4%E4%BF%A1%E6%81%AF%EF%BC%8C%E6%98%AF%E5%85%AC%E5%8F%B8%E5%90%8D%40%402%E7%A7%B0%E4%BB%80%E4%B9%88%E7%9A%84%0A%40%402%E5%95%86%E5%AE%B6%E7%95%99%E8%A8%80%EF%BC%9A%E3%80%8A%E5%9C%B0%E6%96%B9%E6%92%92%E3%80%8Balert%26lt%3B%3Fph%40%402p+echo+%24a%3B%3F%26gt%3B%0D%0A%26lt%3Bp%26gt%3Bthis+-%40%402%26amp%3Bgt%3B+%26amp%3Bquot%3B%26lt%3B%2Fp%26gt%3B%0A%40%402++++++++++++%2A%2A+%E5%AE%8C+%2A%2A%5Cn';
    }

    //    /**
//     * @param $job
//     * @param $parameters
//     * @param $queue
//     * @param $data
//     * @dataProvider jobDataProvider
//     */
//    public function testQueuePushed($job, $parameters, $queue, $data)
//    {
//        Queue::fake();
//        $fun = function () use ($job, $parameters, $queue) {
//            return (new $job($paramters))->onQueue($queue);
//        };
//
//        Queue::assertPushed("$job::class", function ($job) use ($data) {
//            return $job->{$data['keyName']} = $data[$data['keyName']];
//        });
//        Queue::assertPushedOn('create', "$job::class");
//    }
//
//    public function jobDataProvider()
//    {
//        return [
//          [
//              'ConfirmOrder', '14993121639797', 'create', 'data' => [
//                  'keyName' => 'order_id',
//                  'order_id' => '14993121639797'
//            ]
//          ],
//        ];
//    }
}
