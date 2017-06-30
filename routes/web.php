<?php

Route::get('/hadoop/dfshealth.jsp', function () {
    return "I'm health";
});

Route::get('/', function() {
    return ['errno' => 403, 'error' => '未认证'];
});

Route::any('/wechat', 'WechatController@serve');

Route::get('/users', 'UserController@users');
Route::get('/user/{openId}', 'UserController@user');
Route::get('/user/remark', 'UserController@remark');

Route::get('/menu', 'MenuController@menu');
Route::get('/menu/all', 'MenuController@all');
Route::get('/menu/current', 'MenuController@current');

Route::group(['middleware' => ['web', 'wechat.oauth']], function () {
    Route::get('/user', function () {
        $user = session('wechat.oauth_user'); // 拿到授权用户资料

        dd($user);
    });
});
