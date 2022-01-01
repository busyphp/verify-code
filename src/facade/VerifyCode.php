<?php

namespace BusyPHP\verifycode\facade;

use think\Facade;

/**
 * 工厂类
 * @author busy^life <busy.life@qq.com>
 * @copyright (c) 2015--2022 ShanXi Han Tuo Technology Co.,Ltd. All rights reserved.
 * @version $Id: 2022/1/1 下午12:37 VerifyCode.php $
 * @mixin \BusyPHP\verifycode\model\VerifyCode
 * @method static string create(string|int $accountType, string|int $account, string|int $codeType, int $shape = null) 生成验证码
 * @method static void check(string|int $accountType, string|int $account, string|int $codeType, string|int $code, bool $clear = true) 校验验证码
 * @method static void clear(string|int $accountType = '', string|int $account = '', string|int $codeType = '') 清理验证码
 */
class VerifyCode extends Facade
{
    protected static function getFacadeClass()
    {
        return \BusyPHP\verifycode\model\VerifyCode::class;
    }
}