<?php

namespace App\Factories;

use Illuminate\Support\Facades\Input;
use App\Http\Controllers\OrderController;

class ClassFactory
{
    public function applyMethod($cmd)
    {
        $order = app(OrderController::class);

        switch ($cmd) {
            case 'order.create':
                return $order->order();
                break;
            case 'order.confirm':
                $source = Input::get('source');
                $order_id = json_decode(Input::get('body'))->order_id;
                
                return $order->confirm($source, $order_id);
                break;
            case 'order.cancel':
                return $order->cancel();
                break;
            case 'order.status.push':
            case 'order.status.get':
                return $order->status();
                break;
            case 'order.complete':
                return $order->complete();
                break;
            case 'order.get':
                return $order->detail();
                break;
            default:
                $tips = '你不担心我封你 ip 吗？注意一点哦，你的 ip 已被记录';
                info($tips . request()->ip());
                return response(json_encode($tips, JSON_UNESCAPED_UNICODE));
                break;
        }
    }
}
