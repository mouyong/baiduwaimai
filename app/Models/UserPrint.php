<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPrint extends Model
{
    protected $table = 'user_print';
    protected $guarded = ['id'];

    public function baiduShopMachine()
    {
        return $this->hasOne(BaiduShopMachine::class, 'machine_id');
    }

    public function original()
    {
        return $this->hasOne(OutPrint::class, 'printid', 'mkey');
    }
}
