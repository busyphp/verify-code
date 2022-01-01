<?php

namespace BusyPHP\verifycode\model;

use BusyPHP\helper\TransHelper;

/**
 * 验证码模型信息结构
 * @author busy^life <busy.life@qq.com>
 * @copyright (c) 2015--2022 ShanXi Han Tuo Technology Co.,Ltd. All rights reserved.
 * @version $Id: 2021/12/31 下午5:10 SystemVerifyCodeInfo.php $
 */
class VerifyCodeInfo extends VerifyCodeField
{
    /**
     * 格式化的创建时间
     * @var string
     */
    public $formatCreateTime;
    
    
    public function onParseAfter()
    {
        $this->formatCreateTime = TransHelper::date($this->createTime);
    }
}