<?php

namespace App\Http\Controllers;

use App\Jobs\ConfirmOrder;
use App\Models\Record;
use App\Models\BaiduShopMachine;
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
            \Cache::forget('order:' . $body['order_id']);
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
            \Cache::forget('shop:'. $this->baidu->shop_id);
        }

        $res = $this->baidu->setAuth($source)->buildRes('resp.order.create', compact('source_order_id'), 0);
        return $res;
    }

    public function reprint($orderId)
    {
        $data = $this->getDetail($orderId);
        if (empty($data)) {
            $args = [
                'cmd' => 'shop.status.push',
                'body' => [
                    'order_id' => $orderId,
                    'status' => 5,
                ],
            ];

            // $res = $this->baidu->send($args, status_push_url());
            // dd($args, $res);

            return response()->json([
                'errno' => 404,
                'error' => 'order not found',
            ]);
        }

        self::printer($data['shop_info'], $data['order_detail'], $data['order_id'], $data['source']);
        return response()->json([
            'errno' => 0,
            'error' => 'success',
        ]);
    }

    public function getDetail($orderId)
    {
        $order = Record::with('shop')->orderId($orderId)->first();
        // 订单不存在
        if (is_null($order)) {
            return [];
        }

        $shop = $order->shop;
        $user = $shop->user;

        $data['shop_info'] = $shop->toArray();
        $data['shop_info']['api_key'] = $user->apikey;
        unset($data['shop_info']['user']);

        $machines = BaiduShopMachine::machines($shop->id)->get();

        $data['order_id'] = $order->order_id;
        $data['order_detail'] = json_decode($order->order_detail, true);

        $data['source'] = $order->source;

        $data['shop_info']['machines'] = collect($machines)->reduce(function ($c, $i) {
            $original = $i->machine->original;
            $data['id'] = $original->id;
            $data['mkey'] = $original->printid;
            $data['msign'] = $original->password;

            $versionName = $original->version;
            $data['version'] = call_user_func([$this, 'versionNameToInt'], $versionName);

            $c[] = $data;
            return $c;
        });

        if ($shop->fonts_set == 'custom') {
            $setting = $shop->setting;
            $data['shop_info']['fonts_setting'] = $setting->toArray();
        } else {
            $data['shop_info']['fonts_setting'] = [
                "receive_info_size" => 2,
                "receive_address_size" => 2,
                "order_size" => 1,
                "create_order_size" => 1,
                "remark_size" => 2,
                "product_size" => 2,
                "mn" => 1,
                "default" => 2,
            ];
        }

        return $data;
    }

    public function versionNameToInt($versionName)
    {
        switch (strtolower($versionName)) {
            case 'k1':
            case 'k2':
            case 'k3':
            case 'old_print':
                return 0;
                break;
            case 'k2s':
            case 'k3s':
            case 'm1':
            case 'k4':
                return 1;
                break;
            case 'w1':
                return 2;
                break;
            default:
                return 0;
                break;
        }
    }

    public function __call($method, $parameters)
    {
        return $this->baidu->{$method}(...$parameters);
    }
}
