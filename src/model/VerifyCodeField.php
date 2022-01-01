<?php

namespace BusyPHP\verifycode\model;

use BusyPHP\model\Entity;
use BusyPHP\model\Field;

/**
 * 验证码模型字段
 * @author busy^life <busy.life@qq.com>
 * @copyright (c) 2015--2022 ShanXi Han Tuo Technology Co.,Ltd. All rights reserved.
 * @version $Id: 2021/12/31 下午5:09 SystemVerifyCodeField.php $
 * @method static Entity id($op = null, $value = null) ID
 * @method static Entity code($op = null, $value = null) 验证码
 * @method static Entity codeType($op = null, $value = null) 验证码类型
 * @method static Entity createTime($op = null, $value = null) 创建时间
 * @method static Entity accountType($op = null, $value = null) 账号类型
 * @method static Entity account($op = null, $value = null) 账号
 * @method static Entity ip($op = null, $value = null) IP
 */
class VerifyCodeField extends Field
{
    /**
     * ID
     * @var string
     */
    public $id;
    
    /**
     * 验证码
     * @var string
     */
    public $code;
    
    /**
     * 验证码类型
     * @var string
     */
    public $codeType;
    
    /**
     * 创建时间
     * @var int
     */
    public $createTime;
    
    /**
     * 账号类型
     * @var string
     */
    public $accountType;
    
    /**
     * 账号
     * @var string
     */
    public $account;
    
    /**
     * IP
     * @var string
     */
    public $ip;
}