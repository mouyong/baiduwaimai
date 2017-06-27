<?php

Route::get('/', function() {
    return ['errno' => 403, 'error' => '未认证'];

    $str = '{"body":{"errno":0,"error":"success","data":{"source":"64824","shop":{"id":"1717041709","name":"\u6d4b\u8bd5\u8425\u95002","baidu_shop_id":"1717041709"},"order":{"expect_time_mode":1,"pickup_time":0,"atshop_time":0,"delivery_time":0,"delivery_phone":"","finished_time":"0","confirm_time":"1497421378","meal_num":"","commission":427,"order_id":"14974213706360","order_index":"6","status":5,"send_immediately":1,"send_time":"1","send_fee":500,"package_fee":1600,"discount_fee":0,"total_fee":43161,"shop_fee":42734,"user_fee":43161,"responsible_party":"","pay_type":1,"pay_status":1,"need_invoice":2,"invoice_title":"","remark":"\u5c11\u653e\u76d0,\u591a\u653e\u918b,\u4e0d\u5403\u849c,\u591a\u653e\u996d,\u5c11\u653e\u8fa3,\u8fd9\u91cc\u662f\u5907\u6ce8\u4fe1\u606f","delivery_party":2,"create_time":"1497421370","cancel_time":"0"},"user":{"name":"\u725f\u52c7","phone":"18698349096","gender":1,"address":"\u68d5\u6988\u5357\u5cb8 4\u680b3\u5355\u5143404\u53f7","province":"\u56db\u5ddd\u7701","city":"\u6210\u90fd\u5e02","district":"\u53cc\u6d41\u53bf","coord":{"longitude":104.078686,"latitude":30.518158}},"products":[[{"baidu_product_id":"1929183138","other_dish_id":"1929183070","upc":"","product_name":"\u6d4b\u8bd5","product_type":1,"product_price":1,"product_amount":1,"product_fee":1,"package_price":200,"package_amount":"1","package_fee":200,"total_fee":201,"product_attr":[],"product_features":[]},{"baidu_product_id":"2001004824","other_dish_id":"2001004764","upc":"","product_name":"\u5341\u4e94\u5b57\u83dc\u540d\u5c0f\u7092\u624b\u6495\u9e21\u571f\u8c46\u7096\u725b\u8089","product_type":1,"product_price":2000,"product_amount":2,"product_fee":4000,"package_price":200,"package_amount":"0.66","package_fee":264,"total_fee":4264,"product_attr":[],"product_features":[]},{"baidu_product_id":"1926765806","other_dish_id":"1926765727","upc":"","product_name":"\u540c\u6b65\u83dc","product_type":1,"product_price":1000,"product_amount":1,"product_fee":1000,"package_price":200,"package_amount":"1","package_fee":200,"total_fee":1200,"product_attr":[],"product_features":[]}],[{"baidu_product_id":"1928505458","other_dish_id":"1928505393","upc":"","product_name":"Dan","product_type":1,"product_price":780,"product_amount":1,"product_fee":780,"package_price":200,"package_amount":"1","package_fee":200,"total_fee":980,"product_attr":[],"product_features":[]}],[{"baidu_product_id":"1928505458","other_dish_id":"1928505393","upc":"","product_name":"Dan","product_type":1,"product_price":780,"product_amount":1,"product_fee":780,"package_price":200,"package_amount":"1","package_fee":200,"total_fee":980,"product_attr":[],"product_features":[]},{"baidu_product_id":"1926765806","other_dish_id":"1926765727","upc":"","product_name":"\u540c\u6b65\u83dc","product_type":1,"product_price":1000,"product_amount":1,"product_fee":1000,"package_price":200,"package_amount":"1","package_fee":200,"total_fee":1200,"product_attr":[],"product_features":[]},{"baidu_product_id":"1927979756","product_id":"","product_type":2,"product_name":"\u6211\u662f\u5957\u9910","is_fixed_price":"0","product_price":33500,"product_amount":1,"product_fee":33500,"package_price":200,"package_amount":"1","package_fee":200,"total_fee":33700,"group":[{"group_name":"1","baidu_group_id":"1927979757","product":[{"baidu_product_id":"1824257216","other_dish_id":"1824257216","upc":"","product_name":"\u4f9b\u5e94\u5546\u591a\u89c4\u683c","product_type":1,"product_price":33400,"product_amount":1,"product_fee":33400,"product_attr":[],"product_features":[]}]},{"group_name":"2","baidu_group_id":"1927979758","product":[{"baidu_product_id":"1824257213","other_dish_id":"1824257213","upc":"","product_name":"\u4f9b\u5e94\u5546\u5e93\u5b58\u6d4b\u8bd5","product_type":1,"product_price":100,"product_amount":1,"product_fee":100,"product_attr":[],"product_features":[{"baidu_feature_id":"1824257817","name":"df","option":"dfd"}]}]}]}]],"discount":[]}},"cmd":"resp.order.get","encrypt":"","sign":"05D503D3F8ADC07B1FE4CC249568DF79","source":"64824","ticket":"2424782E-CE1B-1F27-619E-04308EFB2BB3","timestamp":1497421429,"version":"3"}';

    $data = \App\Traits\Order::getPrintData(
        json_decode($str, 1)
    );

    class test {
        use \App\Traits\Order;
        public function __construct()
        {
            $this->source = 64824;
        }
    }

    $shopInfo = (new test)->shopInfoFromCache(64824);
    dd($shopInfo);

    $content = \App\Traits\Ylymub::getFormatMsg(
        $data,
        $shopInfo
    );

    dd($content, $shopInfo);
    // \App\Traits\Printer::print($content, $shopInfo);
    dd($data, $string, $content);

    return $data;
});

Route::get('/cookie', function () {
    return view('cookie');
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