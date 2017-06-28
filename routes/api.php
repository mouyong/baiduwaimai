<?php

use Illuminate\Support\Facades\Input;

Route::post('/order', function () {
    return apply_method(Input::get('cmd'));
});

Route::get('/order', function () {
    abort(404);
});

Route::group(['middleware' => ['cors']], function () {
    Route::post('/notify/{id}', function($id) {
        dispatch((new \App\Jobs\UpdateShopInfoToCache($id))->onQueue('update'));
        return ['errno' => 0, 'error' => 'success'];
    });

    Route::post('/shop.get/{shop_id}', 'BaiduController@shop');
    // 登录百度，进行授权
    Route::post('/shop.authorized/{shop_id}', 'BaiduController@authorized');
});
