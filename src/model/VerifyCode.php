<?php

namespace BusyPHP\verifycode\model;

use BusyPHP\App;
use BusyPHP\exception\VerifyException;
use BusyPHP\helper\StringHelper;
use BusyPHP\Model;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;

/**
 * 验证码模型
 * @author busy^life <busy.life@qq.com>
 * @copyright (c) 2015--2022 ShanXi Han Tuo Technology Co.,Ltd. All rights reserved.
 * @version $Id: 2021/12/31 下午5:05 SystemVerifyCode.php $
 * @method VerifyCodeInfo getInfo($data, $notFoundMessage = null)
 * @method VerifyCodeInfo findInfo($data = null, $notFoundMessage = null)
 * @method VerifyCodeInfo[] selectList()
 * @method VerifyCodeInfo[] buildListWithField(array $values, $key = null, $field = null) : array
 */
class VerifyCode extends Model
{
    use VerifyCodeConfig;
    
    /** @var int 大小写字母组合 */
    const SHAPE_DEFAULT = 0;
    
    /** @var int 纯数字 */
    const SHAPE_NUMBER = 1;
    
    /** @var int 大写字母 */
    const SHAPE_CAPITALIZE = 2;
    
    /** @var int 小写字母 */
    const SHAPE_LOWERCASE = 3;
    
    /** @var int 中文 */
    const SHAPE_ZH = 4;
    
    /** @var int 字母数字混合 */
    const SHAPE_LETTER_NUMBER = 5;
    
    public    $name           = 'plugin_verify_code';
    
    protected $bindParseClass = VerifyCodeInfo::class;
    
    
    /**
     * 获取验证码形式
     * @param int|null $shape
     * @return array|string
     */
    public static function getShapes(?int $shape = null)
    {
        return self::parseVars(self::parseConst(self::class, 'SHAPE_', [], 'name'), $shape);
    }
    
    
    /**
     * 生成验证码
     * @param string $accountType 账号类型
     * @param string $account 账号
     * @param string $codeType 验证码类型
     * @param int    $shape 验证码形式
     * @return string
     * @throws DataNotFoundException
     * @throws DbException
     */
    public function create($accountType, $account, $codeType, ?int $shape = null) : string
    {
        $data              = VerifyCodeField::init();
        $data->accountType = trim($accountType);
        $data->account     = trim($account);
        $data->codeType    = trim($codeType);
        $data->id          = self::buildId($data->accountType, $data->account, $data->codeType);
        
        if ($info = $this->findInfo($data->id)) {
            // 检测是否可重发
            $repeatTime  = $info->createTime + $this->getVerifyCodeRepeat($data->accountType);
            $currentTime = time();
            if ($repeatTime > $currentTime) {
                $surplus = $repeatTime - $currentTime;
                throw new VerifyException("请于{$surplus}秒后重试", 'later_send');
            }
        }
        
        $shape            = is_null($shape) ? $this->getVerifyCodeShape($data->accountType) : $shape;
        $data->code       = StringHelper::random($this->getVerifyCodeLength($data->accountType), $shape);
        $data->createTime = time();
        $data->ip         = App::getInstance()->request->ip();
        $this->addData($data, true);
        
        return $data->code;
    }
    
    
    /**
     * 清理验证码
     * @param string $accountType 账号类型
     * @param string $account 账号
     * @param string $codeType 验证码类型
     * @throws DbException
     */
    public function clear($accountType, $account = '', $codeType = '')
    {
        if ($account) {
            $this->deleteInfo(self::buildId($accountType, $account, $codeType));
        }
        
        // 清理过期验证码
        $expireTime = time() - $this->getVerifyCodeExpire($accountType);
        $this->whereEntity(VerifyCodeField::createTime('<=', $expireTime))->delete();
    }
    
    
    /**
     * 校验验证码
     * @param string $accountType 账号类型
     * @param string $account 账号
     * @param string $codeType 验证码类型
     * @param string $code 验证码
     * @param bool   $clear 验证完成后是否清理验证码
     * @throws DataNotFoundException
     * @throws DbException
     */
    public function check($accountType, $account, $codeType, $code, bool $clear = true)
    {
        if (!$info = $this->findInfo(self::buildId($accountType, $account, $codeType))) {
            throw new VerifyException('验证码不正确', 'need_send');
        }
        
        if ($info->createTime + $this->getVerifyCodeExpire(trim($accountType)) <= time()) {
            if ($clear) {
                $this->clear($accountType, $account, $codeType);
            }
            
            throw new VerifyException('验证码已过期', 'expired');
        }
        
        $code = trim($code);
        if ($info->code !== $code) {
            throw new VerifyException('验证码不正确', 'bad');
        }
        
        if ($clear) {
            $this->clear($accountType, $account, $codeType);
        }
    }
    
    
    /**
     * 生成ID
     * @param int    $type
     * @param string $account
     * @param string $codeType
     * @return string
     */
    public static function buildId($type, $account, $codeType) : string
    {
        $type     = trim($type);
        $account  = trim($account);
        $codeType = trim($codeType);
        
        return md5("{$type}_{$account}_{$codeType}");
    }
}