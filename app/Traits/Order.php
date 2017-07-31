<?php

namespace App\Traits;

use App\Jobs\PrintOrder;
use Illuminate\Support\Facades\Input;

trait Order
{
    use Http;

    /**
     * 确认订单
     *
     * @param string $order_id
     * @return array
     */
    public function confirm($order_id)
    {
        $params = self::buildCmd('order.confirm', compact('order_id'));
        return self::send($params);
    }

    /**
     * 取消订单
     *
     * @param string $reason
     * @param string|integer $type
     * @return mixed
     */
    public function cancel($reason = 'Manual cancellation', $type = "-1")
    {
        $source = Input::get('source');

        $body['order_id'] = Input::get('order_id');
        $body['type'] = $type;
        $body['reason '] = $reason;

        $cache_key = 'bdwm:order:' . $body['order_id'];
        if (\Cache::has($cache_key)) {
            \Cache::forget($cache_key);
        }

        $param = $this->setAuth($source)->buildCmd('order.cancel', $body);
        $res = $this->send($param);
        return $res;
    }

    /**
     * 完成订单
     *
     * @return mixed
     */
    public function complete()
    {
        $order_id = Input::get('order_id');
        $source = Input::get('source');

        $param = $this->setAuth($source)->buildCmd('order.complete', compact('order_id'));
        $res = $this->send($param);
        return $res;
    }

    /**
     * 获取订单状态
     */
    public function status()
    {
        $cmd = Input::get('cmd');
        $source = Input::get('source');

        switch ($cmd) {
            case 'order.status.get':
                return self::statusGet(Input::get('order_id'), $source);
                break;
            case 'order.status.push':
                return self::statusPush(json_decode(Input::get('body'), true), $source);
                break;
        }
    }

    private function statusGet($order_id, $source)
    {
        $param = $this->setAuth($source)->buildCmd('order.status.get', compact('order_id'));
        return self::send($param);
    }

    private function statusPush($body, $source)
    {
        // 获取订单详情
        $detail = self::detailFromCache($body['order_id'], $source);
        $this->shop_id = $detail['data']['shop']['baidu_shop_id'];
        // 获取商店信息
        $shopInfo = self::shopInfoFromCache($this->shop_id);

        // 授权了。但是未与账户绑定上
        if (is_null($shopInfo)) {
            $data['errno'] = '-1';
            $data['error'] = '该商户授权了。但是未与账户绑定上';
            $data['shop_id'] = $this->shop_id;
            $data['mission_order_detail'] = $detail;
            throw new \RuntimeException(json_encode($data, JSON_UNESCAPED_UNICODE));
        }

        switch ((int) $body['status']) {
            // 订单已确认
            case 5:
                // 打印订单，存储订单
                self::printer($shopInfo, $detail, $body['order_id'], $source);
                break;
            case 9:
            case 10:
                if (\Cache::has('bdwm:order:'.$body['order_id'])) {
                    \Cache::forget('bdwm:order:'.$body['order_id']);
                }
                break;
        }

        $data['errno'] = 0;
        $data['error'] = 'success';
        return $this->setAuth($source)->buildCmd('resp.order.status.push', $data, 0);
    }

    private function printer($shopInfo, $detail, $order_id, $source)
    {
        // 检查是否有绑定打印机
        self::check_printer($shopInfo, $source);

        // 订单版本对应内容
        $order = [];
        // 同一台终端，同一份订单打印的次数
        $mn = $shopInfo['fonts_setting']['mn'];

        // 遍历用户的所有终端，根据终端版本，传输转化完成后的内容进行打印
        do {
            foreach ($shopInfo['machines'] as $key => $machine) {
                // 判断是否需要转换订单内容
                if (!array_key_exists($machine['version'], $order)) {
                    // 让打印机版本对上转换后的内容
                    $content  = Ylymub::getFormatMsg(
                        // 从订单详情中获取需要格式化的数据
                        self::getPrintData($detail),
                        $shopInfo,
                        $key
                    );
                    $order[$machine['version']] = $content;
                } else {
                    // 已经转换过内容，直接取出来打印
                    $content = $order[$machine['version']];
                }

                // 每处理完一个终端，就将订单进行打印
                dispatch((new PrintOrder($shopInfo, $content, $key, $order_id, $source))->onQueue('print'));
            }
        } while (--$mn);

        \Cache::forget('bdwm:order:'.$order_id);
    }

    private function check_printer($shopInfo, $source)
    {
        // 店家没有易联云打印机，无法打印
        if (!$shopInfo['machines']) {
            $baidu_shop = $this->loadShopFrom($shopInfo['baidu_shop_id'], $source);

            $data['errno'] = -1;
            $data['error'] = 'No printer added';
            $data['data'] = [
                'yilianyun_user' => $shopInfo['user_id'],
                'baidu_shop_id' => $shopInfo['baidu_shop_id'],
                'shop_name' => $baidu_shop['name'],
                'shop_phone' => $baidu_shop['phone'],
                'shop_service_phone' => $baidu_shop['service_phone'],
            ];
            throw new \RuntimeException(json_encode($data, JSON_UNESCAPED_UNICODE));
        }
    }

    public function loadShopFrom($baidu_shop_id, $source, $expire = 3600)
    {
        return \Cache::remember('baidu:shop:detail:' . $baidu_shop_id, $expire, function () use ($baidu_shop_id, $source) {
            return app('baidu', (array) $source)->getShopInfo($baidu_shop_id)['body']['data'];
        });
    }

    /**
     * 获取订单详情
     *
     * @param string $order_id
     * @param string $source
     * @param int $expire
     * @param string $cache_key
     * @return mixed
     */
    public function detailFromCache($order_id, $source, $expire = 60, $cache_key = 'bdwm:order:')
    {
        if (!$order_id) {
            throw new \InvalidArgumentException('Parameter missing order id');
        }

        $cache_key .= $order_id;
        return \Cache::remember($cache_key, $expire, function () use ($order_id, $source) {
            $response = self::send(
                self::setAuth($source)->buildCmd('order.get', compact('order_id'))
            );
            if ($response['body']['errno'] == 0) {
                return $response['body'];
            }

            throw new \RuntimeException('No order detail were obtained：order_id = ' . $order_id);
        });
    }

    /**
     * 获取需要打印的数据
     *
     * @param array $tmpData
     * @return mixed
     */
    public static function getPrintData(array $tmpData)
    {
        $tmpData = $tmpData['data'];

        // 订单当日流水号
        $data['order_index'] = $tmpData['order']['order_index'];

        // 头部信息
        $data['pay_type'] = ((int) $tmpData['order']['pay_type'] === 1) ? '--货到付款--' : '--在线支付--';
        // 付款类型 1 下线 2 在线 要改回来
        // 百度商户名称
        $data['shop_name'] = $tmpData['shop']['name'];
        // 下单时间
        $data['confirm_time'] = '下单时间:' . date('Y年m月d日H时i分', $tmpData['order']['create_time']);
        // 订单编号
        $data['order_id'] = '订单编号:' . $tmpData['order']['order_id'];

        // 是否立即送达
        $data['send_immediately'] = $tmpData['order']['send_immediately'];
        // 预订单
        if (! static::isImmediately($tmpData)) {
            $data['book_order'] = '[ 预订单 ]';
            $data['send_time'] = '期望送达时间:'.date('Y年m月d日H时i分', $tmpData['order']['send_time']);
        }

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

        // 纳税人识别号
        $data['taxer']['taxer_id'] = $tmpData['order']['taxer_id'];
        // 纳税人 抬头信息
        $data['taxer']['invoice_title'] = $tmpData['order']['invoice_title'];

        return $data;
    }

    /**
     * 是否立即送餐
     *
     * @param array $order
     * @return bool
     */
    public static function isImmediately(array $order)
    {
        if ($order['order']['send_immediately'] == 1) {
            return true;
        }
        return false;
    }

    /**
     * 获取所有口袋对应的需要打印的信息
     *
     * @param array $tmpdata
     * @return array
     */
    private static function getProduct(array $tmpdata) {
        $data = [];
        foreach ($tmpdata as $num => $item) {
            foreach ($item as $product) {
                self::package($num, $product, $data);
            }
        }
        return $data;
    }

    private static function package($num, $product, &$data)
    {
        // 产品名称
        $str = $product['product_name'];
        // 套餐 true
        // 不是 false
        $package = !empty($product['group']);

        // 不是套餐的时候先调用 attr .拼接规格
        if (!$package) {
            self::attr($product, $str);
        }
        // 名称结束
        $str .= '[]';

        // 产品份数
        $str .= 'x' . $product['product_amount'] . '[]';
        // 产品份数所对应的总价
        $str .= getNumber($product['product_fee']) . '{}';

        // 是套餐的时候，后调用
        if ($package) {
            $packProduct = $product['group'];
            foreach ($packProduct as $item) {
                $str .= $item['group_name'] .': ';
                foreach ($item['product'] as $value) {
                    $str .= '  ' . $value['product_name'];
                    // 拼接规格
                    self::attr($value, $str);

                    // 名称结束
                    $str .= '[]';

                    // 产品份数
                    $str .= 'x' . $value['product_amount'] . '[]';
                    // 产品份数所对应的总价
                    $str .= getNumber($value['product_fee']) . '{}';

                }
            }
        }

        $data[$num][] = $str;
        return $data;
    }

    private static function attr($product, &$str)
    {
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

            $str = mb_substr($str, 0, -1) . '))';
        }
    }
}
