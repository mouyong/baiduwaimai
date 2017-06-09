<?php

namespace App\Factories;

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
                return 'call here';
                break;
        }
    }
}
