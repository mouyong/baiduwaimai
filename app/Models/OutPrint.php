<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutPrint extends Model
{
    protected $table = 'out_print';
    protected $guarded = ['id'];

    public function machine()
    {
        return $this->belongsTo(UserPrint::class, 'printid', 'mkey');
    }
}
