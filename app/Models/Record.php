<?php

namespace App\Models;

use App\Models\Traits\OrderTrait;
use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    protected $guarded = ['id'];

    use OrderTrait;

    public function shop()
    {
        return $this->belongsTo(BaiduShop::class, 'baidu_shop_id', 'baidu_shop_id');
    }
}
