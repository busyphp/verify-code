<?php
declare(strict_types = 1);

namespace BusyPHP\facade;

use BusyPHP\verifycode\model\PluginVerifyCode;
use think\Facade;

/**
 * 工厂类
 * @author busy^life <busy.life@qq.com>
 * @copyright (c) 2015--2022 ShanXi Han Tuo Technology Co.,Ltd. All rights reserved.
 * @version $Id: 2022/1/1 下午12:37 VerifyCode.php $
 * @mixin PluginVerifyCode
 * @method static string create(string $accountType, string $account, string $codeType = '', int $style = null) 生成验证码
 * @method static void check(string $accountType, string $account, string $code, string $codeType = '', bool $clear = true) 校验验证码
 * @method static void clear(string $accountType, string $account = '', string $codeType = '') 清理验证码
 * @method static array|null getAccountTypeMap(string $accountType) 获取账户类型映射
 * @method static mixed getCodeConfig(string $accountType, string $name, mixed $default = null) 获取验证码配置
 * @method static int getCodeExpire(string $accountType) 获取验证码有效时长
 * @method static int getCodeRepeat(string $accountType) 获取可重发验证码时长
 * @method static int getCodeStyle(string $accountType) 获取验证码样式
 * @method static int getCodeLength(string $accountType) 获取验证码长度
 */
class VerifyCode extends Facade
{
    /** @var int 大小写字母组合 */
    const STYLE_DEFAULT = PluginVerifyCode::STYLE_DEFAULT;
    
    /** @var int 纯数字 */
    const STYLE_NUMBER = PluginVerifyCode::STYLE_NUMBER;
    
    /** @var int 大写字母 */
    const STYLE_CAPITALIZE = PluginVerifyCode::STYLE_CAPITALIZE;
    
    /** @var int 小写字母 */
    const STYLE_LOWERCASE = PluginVerifyCode::STYLE_LOWERCASE;
    
    /** @var int 中文 */
    const STYLE_ZH = PluginVerifyCode::STYLE_ZH;
    
    /** @var int 字母数字混合 */
    const STYLE_LETTER_NUMBER = PluginVerifyCode::STYLE_LETTER_NUMBER;
    
    
    protected static function getFacadeClass()
    {
        return PluginVerifyCode::class;
    }
}