<?php

use Illuminate\Support\Facades\Input;

Route::post('/order', function () {
    return apply_method(Input::get('cmd'));
});

Route::get('/order', function () {
    return app('Rcache')->getList('LIST:BAIDU:ORDER', 0, 1);
});

Route::post('/notify', function() {
    dispatch(new \App\Jobs\UpdateShopInfoToCache(Input::get('shop_id')));
})->middleware('cors');
