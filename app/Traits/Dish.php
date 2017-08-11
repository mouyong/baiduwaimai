<?php

namespace App\Traits;

trait Dish
{
    protected $dish;

    public function setDish($shop_id)
    {
        $this->dish = config('dish');
        $this->dish['shop_id'] = $shop_id;
    }

    public function getDish()
    {
        return $this->dish;
    }

    public function dishCreate($source, $secret)
    {
        $dish = $this->getDish();
        $args = $this->source($source)->secret_key($secret)->buildCmd('dish.create', $dish);
        // dd($args);
        $res = $this->send($args);
        dd($res);
    }

    public function dishUpdate($source, $secret)
    {
        $dish = $this->getDish();
        $args = $this->source($source)->secret_key($secret)->buildCmd('dish.update', $dish);
        // dd($args);
        $res = $this->send($args);
        dd($res);
    }

    public function dishMenu($shop_id, $source, $secret)
    {
        $args = $this->source($source)->secret_key($secret)->buildCmd('dish.menu.get', compact('shop_id'));
        // dd($args);
        $res = $this->send($args);
        dd($res);
    }

    public function dishOnline($shop_id, $dish_id, $source, $secret)
    {
        $args = $this->source($source)->secret_key($secret)->buildCmd('dish.online', compact('shop_id', 'dish_id'));
        // dd($args);
        $res = $this->send($args);
        dd($res);
    }
}
