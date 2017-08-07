<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    protected $guarded = ['id'];
    // protected $table = 'recoreds_new';

    public function scopeOrderId($query, $orderId)
    {
        return $query->where('order_id', $orderId);
    }
}
