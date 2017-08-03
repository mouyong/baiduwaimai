<?php

$photos = [
    [
        'photo_url' => 'http://img.waimai.baidu.com/pb/f5076e3caed758a1c18c91a0e9cae3368f',
        'waterprinter_url' => 'http://img.waimai.baidu.com/pb/f5076e3caed758a1c18c91a0e9cae3368f',
    ],
    [
        'photo_url' => 'http://img.waimai.baidu.com/pb/f5076e3caed758a1c18c91a0e9cae3368f',
        'waterprinter_url' => 'http://img.waimai.baidu.com/pb/f5076e3caed758a1c18c91a0e9cae3368f',
    ],
];

$datetime = (new DateTime())->modify('+1 day')->format('Y-m-d');

$aptitude = [
    // 营业执照
    [
        'type' => 1,
        'license_name' => '营业资质测试',
        'license_number' => '19931023',
        'license_validdate' => $datetime,
        'registration_number' => '123456',
        'organization_number' => '123456789',
        'permission_number' => '654321',
        'permission_time' => $datetime,
        'photos' => $photos,
    ],
    // 行业资质，餐饮服务许可证
    [
        'type' => 8,
        'license_name' => '行业资质，餐饮服务许可证测试',
        'license_number' => '14235',
        'license_validdate' => $datetime,
        'registration_number' => '123456',
        'organization_number' => '123456789',
        'permission_number' => '654321',
        'permission_time' => $datetime,
        'photos' => $photos,
    ],
];

return [
    'shop_id' => '',
    'aptitude' => $aptitude,
];
