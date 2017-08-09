<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaiduShopMachine extends Model
{
    protected $guarded = ['id'];
    
    public function machine()
    {
        return $this->belongsTo(UserPrint::class, 'machine_id');
    }
    
    public function shop()
    {
        return $this->belongsTo(BaiduShop::class, 'baidu_shop_id');
    }

    public function scopeMachines($query, $baiduShopId)
    {
        return self::with(['machine' => function ($query) {
            $query->with('original');
        }])->where('baidu_shop_id', $baiduShopId);
    }
}
