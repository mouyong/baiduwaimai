<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopSetting extends Model
{
    protected $table = 'shop_setting';

    public function shop()
    {
        return $this->belongsTo(BaiduShop::class, 'shop_id');
    }
}
