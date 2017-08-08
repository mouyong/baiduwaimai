<?php

namespace App\Models\Traits;

trait OrderTrait
{
    public function scopeOrderId($query, $orderId)
    {
        return $query->where('order_id', $orderId);
    }
}
