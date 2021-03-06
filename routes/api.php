<?php

use Illuminate\Support\Facades\Input;

Route::match(['get', 'post'], '/order', function () {
    return apply_method(Input::get('cmd'));
});

// Route::get('/order', function () {
//     return ['errno' => 403, 'error' => 'unauthorized action.'];
// });

Route::get('order.reprint/{order_id}', 'OrderController@reprint');

Route::post('/notify/{id}', 'BaiduController@notify');
Route::post('/shop.get/{shop_id}/{source}', 'BaiduController@shop');
// 登录百度，进行授权
Route::post('/shop.authorized/{shop_id}/{source?}', 'BaiduController@authorized');

Route::get('shop.create/{supplier_id}/{source}/{secret}', 'BaiduController@shopCreate');
Route::get('shop.update/{shop_id}/{source}/{secret}', 'BaiduController@shopUpdate');
Route::get('shop.open/{baidu_shop_id}/{source}/{secret}', 'BaiduController@shopOpen');
Route::get('shop.offline/{baidu_shop_id}/{source}/{secret}', 'BaiduController@shopOffline');
Route::get('shop.close/{baidu_shop_id}/{source}/{secret}', 'BaiduController@shopClose');
Route::get('shop.get/{shop_id}/{source}/{secret}', 'BaiduController@shopGet');

Route::get('shop.aptitude.upload/{shop_id}/{source}/{secret}', 'BaiduController@aptitudeUpload');
Route::get('shop.aptitude.get/{shop_id}/{source}/{secret}', 'BaiduController@aptitudeGet');

Route::get('dish.create/{shop_id}/{source}/{secret}', 'BaiduController@dishCreate');
Route::get('dish.update/{shop_id}/{source}/{secret}', 'BaiduController@dishUpdate');
Route::get('dish.menu.get/{shop_id}/{source}/{secret}', 'BaiduController@dishMenu');
Route::get('dish.online/{shop_id}/{dish_id}/{source}/{secret}', 'BaiduController@dishOnline');
