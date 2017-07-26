<?php

use Illuminate\Support\Facades\Input;

Route::post('/order', function () {
    return apply_method(Input::get('cmd'));
});

Route::get('/order', function () {
    return ['errno' => 403, 'error' => 'unauthorized action.'];
});

Route::post('/notify/{id}', 'BaiduController@notify');
Route::post('/shop.get/{shop_id}/{source}', 'BaiduController@shop');
// 登录百度，进行授权
Route::post('/shop.authorized/{shop_id}/{source?}', 'BaiduController@authorized');
