<?php

namespace App\Traits;

trait KaTestShop
{
    protected $testShop;

    public function setTestShop($shop_id)
    {
        $this->testShop = config('test_shop');
        $this->testShop['shop_id'] = $shop_id;
    }

    public function getTestShop()
    {
        return $this->testShop;
    }

    public function shopCreate($source)
    {
        $shop = $this->getTestShop();
        $args = $this->setAuth($source)->buildCmd('shop.create', $shop);
        // dd($args);
        $res = $this->send($args);
        dd($res);
    }

    public function shopUpdate($source)
    {
        $shop = $this->getTestShop();
        $args = $this->setAuth($source)->buildCmd('shop.update', $shop);
        // dd($args);
        $res = $this->send($args);
        dd($res);
    }

    public function shopGet($shop_id, $source)
    {
        $args = $this->setAuth($source)->buildCmd('shop.get', compact('shop_id'));
        // dd($args);
        $res = $this->send($args);
        dd($res);
    }

    public function setAptitude($shop_id)
    {
        $this->aptitude = config('aptitude');
        $this->aptitude['shop_id'] = $shop_id;
    }

    public function getAptitude()
    {
        return $this->aptitude;
    }

    public function aptitudeUpload($source)
    {
        $args = $this->setAuth($source)->buildCmd('shop.aptitude.upload', $this->getAptitude());
        // dd($args, $this->getAptitude());
        $res = $this->send($args);
        dd($res);
    }

    public function aptitudeGet($shop_id, $source)
    {
        $args = $this->setAuth($source)->buildCmd('shop.aptitude.get', compact('shop_id'));
        // dd($args);
        $res = $this->send($args);
        dd($res);
    }
}
