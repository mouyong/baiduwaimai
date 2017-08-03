<?php

$available_times = [
    '*' => [
        [
            'start' => '00:00',
            'end' => '23:59',
        ],
    ]
];

$category = [
    [
        'name' => '超值套餐',
        'rank' => 0,
    ],
];

$norms = [
    [
        'name' => '分量',
        'value' => '大盒',
        'price' => 12000,
        'selfid' => 'hmj_001',
        'stock' => 10,
    ],
    [
        'name' => '分量',
        'value' => '中盒',
        'price' => 10000,
        'selfid' => 'hmj_002',
        'stock' => 20,
    ],
];

return [
    'shop_id' => '',
    'dish_id' => '100',
    'name' => '双黄蛋',
    'price' => '10000',
    'pic' => 'http://img.waimai.baidu.com/pb/d2dc661dc90a269ee7fb7ffb66a3bcf0df',
    'min_order_num' => 1,
    'package_box_num' => 1,
    'description' => '双黄蛋',
    'available_times' => $available_times,
    'stock' => 100,
    'category' => $category,
    'norms' => $norms,
];
