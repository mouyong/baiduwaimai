<?php

namespace App\Models;

use App\Models\Traits\OrderTrait;
use App\Models\Traits\UserTrait;
use Illuminate\Database\Eloquent\Model;

class BaiduShop extends Model
{
    protected $guarded = ['id'];

    use OrderTrait;
    use UserTrait;

    public function records()
    {
        return $this->hasMany(Record::class, 'baidu_shop_id', 'baidu_shop_id');
    }

    public function relMachines()
    {
        return $this->hasMany(BaiduShopMachine::class, 'baidu_shop_id');
    }

    public function setting()
    {
        return $this->hasOne(ShopSetting::class, 'shop_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
