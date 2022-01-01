<?php

namespace BusyPHP\verifycode\model;

use BusyPHP\App;
use BusyPHP\app\admin\model\system\plugin\SystemPlugin;
use BusyPHP\helper\ArrayHelper;
use BusyPHP\helper\FilterHelper;
use Throwable;

/**
 * 验证码配置
 * @author busy^life <busy.life@qq.com>
 * @copyright (c) 2015--2022 ShanXi Han Tuo Technology Co.,Ltd. All rights reserved.
 * @version $Id: 2021/12/31 下午9:58 VerifyCodeConfig.php $
 */
trait VerifyCodeConfig
{
    /**
     * 获取配置
     * @param string $name 配置名称
     * @param null   $default 默认值
     * @return mixed
     */
    public function getVerifyCodeConfig($name, $default = null)
    {
        return App::getInstance()->config->get('busy-verify-code' . ($name ? ".{$name}" : ''), $default);
    }
    
    
    /**
     * 获取设置
     * @param string $name 设置名称
     * @param mixed  $default 默认值
     * @return mixed
     */
    public function getVerifyCodeSetting(string $name, $default = null)
    {
        try {
            $settings = SystemPlugin::init()->getSetting('busyphp/verify-code');
        } catch (Throwable $e) {
            $settings = [];
        }
        
        return ArrayHelper::get($settings, $name, $default) ?? $default;
    }
    
    
    /**
     * 获取验证码过期时间秒
     * @param string|int $accountType 账户类型
     * @return int
     */
    public function getVerifyCodeExpire($accountType) : int
    {
        return (int) $this->getVerifyCodeSetting("accounts.{$accountType}.expire", $this->getVerifyCodeConfig("accounts.{$accountType}.expire", 600));
    }
    
    
    /**
     * 获取可重发验证码秒数
     * @param string|int $accountType 账户类型
     * @return int
     */
    public function getVerifyCodeRepeat($accountType) : int
    {
        return (int) $this->getVerifyCodeSetting("accounts.{$accountType}.repeat", $this->getVerifyCodeConfig("accounts.{$accountType}.repeat", 60));
    }
    
    
    /**
     * 获取验证码长度
     * @param string|int $accountType 账户类型
     * @return int
     */
    public function getVerifyCodeLength($accountType) : int
    {
        return (int) $this->getVerifyCodeSetting("accounts.{$accountType}.length", $this->getVerifyCodeConfig("accounts.{$accountType}.length", 6));
    }
    
    
    /**
     * 获取验证码形式
     * @param string|int $accountType 账户类型
     * @return int
     */
    public function getVerifyCodeShape($accountType) : int
    {
        return (int) $this->getVerifyCodeSetting("accounts.{$accountType}.shape", $this->getVerifyCodeConfig("accounts.{$accountType}.shape", VerifyCode::SHAPE_NUMBER));
    }
}