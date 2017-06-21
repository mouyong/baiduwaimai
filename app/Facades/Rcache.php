<?php

namespace App\Facades;

class Rcache
{
    public function newRedisCache()
    {
        return app(\App\Redis\Rcache::class);
    }

    public function __call($method, $parameters)
    {
        return $this->newRedisCache()->$method(...$parameters);
    }

    public static function __callStatic($method, $parameters)
    {
        return (new static)->$method(...$parameters);
    }
}