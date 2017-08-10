<?php

namespace App\Http\Controllers;

use Baidu\Baidu;
use Illuminate\Support\Facades\Input;

class ShopController extends Controller
{
    private $baidu;

    public function __construct(Baidu $baidu)
    {
        $this->baidu = $baidu;
    }

    public function respStatusPush()
    {
        $source = Input::get('source');

        $body = [
            'errno' => 0,
            'error' => 'success',
            'data' => '',
        ];

        $args = $this->baidu->setAuth($source)->buildCmd('resp.shop.status.push', $body);
        return $args;
    }
}
