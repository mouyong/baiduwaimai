<?php

$categorys = [[
    'category1' => 1,
    'category2' => 2,
    'category3' => 3,
]];

$delivery_region_region = [
    [
        [
            'latitude' => '39.914115',
            'longitude' => '116.496274',
        ],
        [
            'latitude' => '39.939622',
            'longitude' => '116.496597',
        ],
        [
            'latitude' => '39.940341',
            'longitude' => '116.501628',
        ],
        [
            'latitude' => '39.942886',
            'longitude' => '116.510539',
        ],
        [
            'latitude' => '39.946841',
            'longitude' => '116.523798',
        ],
        [
            'latitude' => '39.914115',
            'longitude' => '116.496274',
        ],
    ],
    [
        [
            'longitude' => 101.953192,
            'latitude' => 32.862984,
        ],
        [
            'longitude' => 101.879603,
            'latitude' => 28.994299,
        ],
        [
            'longitude' => 108.576219,
            'latitude' => 29.12362,
        ],
        [
            'longitude' => 108.429041,
            'latitude' => 33.111072,
        ],
        [
            'longitude' => 106.147776,
            'latitude' => 33.481882,
        ],
    ],
];

$delivery_region = [[
    'name' => '西二旗配送区',
    'region' => $delivery_region_region,
    'delivery_time' => '60',
    'delivery_fee' => '600',
    'min_buy_free' => '5000',
    'min_order_price' => 2220,
]];

$business_time = [
    [
        'start' => '00:00',
        'end' => '23:59',
    ],
];

return [
    'supplier_id' => '',
    'shop_id' => 666,
    'name' => '百度外卖lgz6',
    'shop_logo' => 'http://api.waimai.baidu.com/WebImage/SupplierImage/2521/s09100000000.jpg',
    'province' => '23',
    'city' => '253',
    'county' => '8308',
    'address' => '北京市海淀区上地信息路甲9号',
    'brand' => '百度外卖',
    'categorys' => $categorys,
    'phone' => '010-59929653',
    'service_phone' => '4008527547',
    'longitude' => '116.524067',
    'latitude' => '39.923371',
    'coord_type' => '',
    'delivery_region' => $delivery_region,
    'business_time' => $business_time,
    'book_ahead_time' => '',
    'invoice_support' => '2',
    'package_box_price' => '0.0000',
    'shop_code' => '2022',
];
