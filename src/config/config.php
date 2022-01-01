<?php

use BusyPHP\verifycode\model\VerifyCode;

return [
    // 设置验证账号类型
    'accounts' => [
        'phone' => [
            'name'   => '短信验证码', // 名称，用于后台设置中展示
            'expire' => 600,//  验证码过期秒数
            'repeat' => 60, //  可重发验证码秒数
            'length' => 6,  //  验证码长度
            'shape'  => VerifyCode::SHAPE_NUMBER
        ]
    ],
];