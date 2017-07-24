<?php

return [
    // 'yilianyun_print_api_url' => env('YILIANYUN_PRINT_API_URL', 'http://114.215.65.36:8888'),
    'yilianyun_print_api_url' => env('YILIANYUN_PRINT_API_URL', 'http://open.10ss.net:8888'),

    'baidu_take_out_api_url' => env('BAIDU_TAKE_OUT_API_URL', 'https://api.waimai.baidu.com'),
    'baidu_shop_info_url' => env('BAIDU_SHOP_INFO_URL', 'http://yilianyun.10ss.net/bdwm/bdwminfo'),
    'baidu_source_info_url' => env('BAIDU_SOURCE_INFO_URL', 'http://yilianyun.10ss.net/bdwm/loadsourceinfobysource'),
    'baidu_no_upper_limit_source_info_url' => env('BAIDU_NO_UPPER_LIMIT_SOURCE_INFO_URL', 'http://yilianyun.10ss.net/bdwm/getonenotupperlimitsource'),

    'baidu' => [
        'login' => [
            'account' => env('BAIDUDEV_ACCOUNT', 13112345678), // 登录百度外卖的账号
            'redirect_url' => 'http://dev.waimai.baidu.com', // 不要改，否则会提示未授权的平台。
            'type' => 1, // 不要改，否则自己去抓手机端的验证方式和接口
            'upass' => env('BAIDUDEV_UPASS', 'EHbwEjMxEFT'), // 登录百度外卖的密码，抓取的。
        ],
        'authorized' => [
            'source' => '', // 绑定的应用 source
            'bindapply_type' => 1, // 绑定类型，1 门店百度ID 2 供应商百度ID
            // 找商户索要权限，2 商品类接口 3 商户类接口 4 菜品类接口 5 订单类接口 6 营销类接口
            'wid' => '', // 商户 id
            'auth_cmd_category' => env('BAIDUDEV_AUTHORIZED', '2,3,4,5,6'),
        ],

    ],

    'bug' => [
        'emails' => env('BUG_ACCEPT_EMAIL', '925544019@qq.com'),
    ],
];
