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
        $secret = secret_key($source);
        if ($source == 64475) $secret = '1096bebd4fc8a45d';

        $body = [
            'errno' => 0,
            'error' => 'success',
            'data' => '',
        ];

        $args = $this->baidu->source($source)->secret_key($secret)->buildCmd('resp.shop.status.push', $body, 0);
        return $args;
    }
}
