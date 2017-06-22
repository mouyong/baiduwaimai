<?php

namespace App\Traits;

use App\Jobs\PrintOrder;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\Rules\In;

trait Order
{
    use Baidu, Shop;

    protected $ticket;

    /**
     * 初始化相关变量
     */
    public function __construct()
    {
        $this->set_baidu();

        $this->ticket = Input::get('ticket');
    }

    /**
     * 确认订单
     *
     * @param string $order_id
     * @param string $ticket
     * @return array
     */
    public function confirm(string $order_id, string $ticket)
    {
        $params = $this->buildCmd('order.confirm', $ticket, compact('order_id'));
        return $this->zttp->post(bd_api_url(), $params)->json();
    }

    /**
     * 取消订单
     *
     * @param string $reason
     * @param string|integer $type
     * @return void
     */
    public function cancel(string $reason = '手动取消', $type = "-1")
    {
        $body['order_id'] = Input::get('order_id');
        $body['type'] = $type;
        $body['reason '] = $reason;
        $this->source = Input::get('source');

        $shopInfo = self::shopInfoFromCache($this->source);
        $this->secret = $shopInfo['baidu_secret_key'];

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

        if ($cmd === 'order.status.push') {
            $body = json_decode(Input::get('body'), true);
            $this->source = Input::get('source');

            $shopInfo = self::shopInfoFromCache($this->source);
            $this->secret = $shopInfo['baidu_secret_key'];

            $order_id = $body['order_id'];
            $order_status = (int) $body['status'];

            // 从百度获取订单详情
            $detail = self::detail($order_id);

            switch ($order_status) {
                // 订单已确认
                case 5:

                    // 根据 shop info 的 商店字体设置，是否为默认，自定义字体大小，进行文本格式化。
                    // 获取格式化后的内容
                    //
                    if (empty($shopInfo['machines'])) {
                        // todo 通知，未添加打印机。
                        return false;
                    }

                    foreach ($shopInfo['machines'] as $key => $machine) {
                        $content = Ylymub::getFormatMsg(
                            // 从订单详情中获取需要格式化的数据
                            Order::getPrintData($detail),
                            $shopInfo
                        );

                        if (!$content) {
                            // todo 发邮件，通知没有绑定信息。
                            return false;
                        }
                        dispatch(new PrintOrder(Input::get('source'), $order_id, $content));
                    }

                    break;
            }

            $data['errno'] = 0;
            $data['error'] = 'success';
            $args = $this->buildCmd('resp.order.status.push', $this->ticket, $data, 0);

            return $args;
        } elseif($cmd === 'order.status.get') {
            $order_id = Input::get('order_id');

            $args = $this->buildRes('order.status.get', $this->ticket, compact('order_id'));
            $this->res = $this->zttp->post(bd_api_url(), $args);
        }
    }

    /**
     * 获取订单详情
     *
     * @param string $order_id
     * @return mixed
     */
    public function detail($order_id = '')
    {
        $order_id = $order_id ?: Input::get('order_id');

        if (!$order_id) {
            throw new \InvalidArgumentException('缺少订单 id');
        }

        $args = $this->buildCmd('order.get', $this->ticket, compact('order_id'));
        $res = $this->zttp->post(bd_api_url(), $args)->json();

        return $res;
    }

    public static function getPrintData($tmpData)
    {
        $tmpData = $tmpData['body']['data'];

        // 订单当日流水号
        $data['order_index'] = $tmpData['order']['order_index'];

        // 头部信息
        $data['pay_type'] = ((int) $tmpData['order']['pay_type'] === 1) ? '--货到付款--' : '--在线支付--';
        // 付款类型 1 下线 2 在线 要改回来
        // 百度商户名称
        $data['shop_name'] = $tmpData['shop']['name'];
        // 下单时间
        $data['confirm_time'] = '下单时间:' . date('Y年m月d日H时i分', $tmpData['order']['confirm_time']);
        // 订单编号
        $data['order_id'] = '订单编号:' . $tmpData['order']['order_id'];

        // 各个口袋对应的商品详情
        $data['product'] = self::getProduct($tmpData['products']);

        // 配送费 & 餐盒费
        // 配送费
        $data['send_fee'] = '配送费:' . getNumber($tmpData['order']['send_fee']);
        // 餐盒费
        $data['package_fee'] = '餐盒费:' . getNumber($tmpData['order']['package_fee']);

        // 总计
        $data['total_fee'] = '小计:￥' . getNumber($tmpData['order']['total_fee']);
        // 优惠总金额
        $data['discount_fee'] = '折扣:￥' . getNumber($tmpData['order']['discount_fee']);

        $first_name = mb_substr($tmpData['user']['name'],0,1);
        $nickname = ($tmpData['user']['gender'] === 1) ? '(先生)' : '(女士)';

        $data['user_fee'] = '订单总价:￥' . getNumber($tmpData['order']['user_fee']);
        $data['address'] = htmlspecialchars_decode($tmpData['user']['address']);
        $data['info'] = $first_name . $nickname . ': ' . offset($tmpData['user']['phone'], [3, 7]);

        // 用餐人数
        if (empty($tmpData['order']['meal_num'])) {
            $userNum = 1;
        } else {
            $userNum = $tmpData['order']['meal_num'];
        }
        // 备注信息
        $data['remark'] = '订单备注：[用餐人数]' . $userNum . '人；';
        $data['remark'] .= $tmpData['order']['remark'];

        return $data;
    }

    /**
     * 获取所有口袋对应的需要打印的信息
     *
     * @param array $tmpdata
     * @return array
     */
    private static function getProduct($tmpdata) {
        $data = [];
        foreach ($tmpdata as $num => $item) {
            foreach ($item as $product) {
                // 产品名称
                $str = $product['product_name'];
                // 拼接规格
                if (count($product['product_attr'])) {
                    $str .= '(';
                    foreach ($product['product_attr'] as $product_attr) {
                        $str .= $product_attr['option'] . '、';
                    }
                    $str = rtrim($str, '、') . ')';
                }

                if (count($product['product_features'])) {
                    if (!strstr($str, '(')) {
                        $str .= '(';
                    } else {
                        $str = rtrim($str, ')') . '、';
                    }

                    foreach ($product['product_features'] as $product_features) {
                        $str .= $product_features['option'] . '、';
                    }
                    $str = rtrim($str, '、') . ')';
                }
                $str .= '[]';

                // 产品份数
                $str .= 'x' . $product['product_amount'] . '[]';
                // 产品份数所对应的总价
                $data[$num][] = $str . getNumber($product['product_fee']) . '{}';
            }
        }
        return $data;
    }
}
