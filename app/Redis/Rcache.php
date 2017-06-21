<?php

namespace App\Redis;

use App\Traits\Singleton;
use Illuminate\Support\Facades\Redis;

class Rcache extends Redis
{
    use Singleton;

    public function put($key, $data)
    {
        Redis::rpush($key, $data);
    }

    public function set($key, $value, $cache = 0)
    {
        Redis::set($key, $value);

        if ($cache) {
            $this->expire($key);
        }
    }

    /**
     * 判断给定值在 list 是否存在
     *
     * @param string $key Redis list key
     * @param string $v 存在与 list 中的某个值
     * @return bool
     */
    public function list_exists($key, $v)
    {
        $arr = $this->getList($key);
        return array_key_exists($v, $arr);
    }

    public function getList($key, $start = 0, $end = -1)
    {
        return Redis::lrange($key, $start, $end);
    }

    public function getOneOfList($key)
    {
        return self::getList($key, 0, 0);
    }

    /**
     * 为 Redis Key 设置过期时间
     *
     * @param string $key
     * @param string $env_expire_key
     * @return mixed
     */
    public function expire($key, $env_expire_key = 'REDIS_EXPIRE')
    {
        return Redis::expire($key, env('REDIS_EXPIRE', env($env_expire_key, 60)));
    }

    public function __call($method, $parameters)
    {
        return Redis::$method(...$parameters);
    }

    public static function __callStatic($method, $parameters)
    {
        return (new static)->$method(...$parameters);
    }
}