验证码验证模块
===============

> 支持短信、邮件等方式验证

## 安装方式

```shell script
composer require busyphp/verify-code
```

> 安装完成后可以通过后台管理 > 开发模式 > 插件管理进行 `安装`

> 配置 `config/admin.php` -> `model` -> 增加 `plugin_verify_code`

```php
<?php
return [
    'model' => [
        // 增加以下
        // 消息验证码模型
        'plugin_verify_code' => [
            // 定义账号类型
            'account_types' => [
                'phone' => [
                    'name'   => '短信验证码', // 名称，用于后台设置中展示
                    'expire' => 600,// 验证码过期秒数
                    'repeat' => 60, // 可重发验证码秒数
                    'length' => 6,  // 验证码长度
                    'style'  => \BusyPHP\facade\VerifyCode::STYLE_NUMBER  // 验证码类型
                ]
            ]
        ],
    ]
];
```

## 生成验证码

```php
$code = \BusyPHP\verifycode\facade\VerifyCode::create('phone', '13333333333', 'login');

// 自定义发送验证码逻辑
$success = send_sms($code); 

// 发送验证码失败
if (!$success) {
    // 删除
    \BusyPHP\verifycode\facade\VerifyCode::clear('phone', '13333333333', 'login');
}

```

## 校验验证码
```php
\BusyPHP\verifycode\facade\VerifyCode::check('phone', '13333333333', 'login', '123456');
```