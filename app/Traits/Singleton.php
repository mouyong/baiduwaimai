<?php

namespace App\Traits;

trait Singleton
{
    private static $instance = null;
    //私有构造函数，防止外界实例化对象
    private function __construct() {}
    //私有克隆函数，防止外办克隆对象
    private function __clone() {}
    //静态方法，单例统一访问入口
    public static function getInstance()
    {
        if (is_null (self::$instance) || isset (self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }
}