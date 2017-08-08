<?php

namespace App\Models\Traits;

trait UserTrait
{
    public function scopeUserId($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
