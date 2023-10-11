<?php
declare(strict_types = 1);

namespace BusyPHP\verifycode\model;

use BusyPHP\app\admin\model\system\config\SystemConfig;
use BusyPHP\exception\VerifyException;
use BusyPHP\helper\ArrayHelper;
use BusyPHP\helper\ClassHelper;
use BusyPHP\helper\StringHelper;
use BusyPHP\Model;
use BusyPHP\model\Entity;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\facade\Config;
use think\facade\Request;

/**
 * 验证码模型
 * @author busy^life <busy.life@qq.com>
 * @copyright (c) 2015--2022 ShanXi Han Tuo Technology Co.,Ltd. All rights reserved.
 * @version $Id: 2021/12/31 下午5:05 SystemVerifyCode.php $
 * @method PluginVerifyCodeField getInfo($data, $notFoundMessage = null)
 * @method PluginVerifyCodeField findInfo($data = null)
 * @method PluginVerifyCodeField[] selectList()
 * @method PluginVerifyCodeField[] indexList(string|Entity $key = 'id')
 * @method PluginVerifyCodeField[] indexListIn(array $range, string|Entity $key = 'id', string|Entity $field = 'id')
 */
class PluginVerifyCode extends Model
{
    /** @var int 大小写字母组合 */
    const STYLE_DEFAULT = 0;
    
    /** @var int 纯数字 */
    const STYLE_NUMBER = 1;
    
    /** @var int 大写字母 */
    const STYLE_CAPITALIZE = 2;
    
    /** @var int 小写字母 */
    const STYLE_LOWERCASE = 3;
    
    /** @var int 中文 */
    const STYLE_ZH = 4;
    
    /** @var int 字母数字混合 */
    const STYLE_LETTER_NUMBER = 5;
    
    public           $name                = 'plugin_verify_code';
    
    protected string $fieldClass          = PluginVerifyCodeField::class;
    
    protected string $dataNotFoundMessage = '消息验证码记录不存在';
    
    protected array  $config;
    
    
    public function __construct(string $connect = '', bool $force = false)
    {
        $this->config = (array) (Config::get('admin.model.plugin_verify_code') ?: []);
        
        parent::__construct($connect, $force);
    }
    
    
    /**
     * 获取验证码样式映射
     * @param int|null $type
     * @return array|string|null
     */
    public static function getStyleMap(int $type = null) : array|string|null
    {
        return ArrayHelper::getValueOrSelf(ClassHelper::getConstAttrs(self::class, 'STYLE_', 'name'), $type);
    }
    
    
    /**
     * 获取账户类型映射
     * @param string|null $accountType 账户类型
     * @return array|null
     */
    public function getAccountTypeMap(string $accountType = null) : array|null
    {
        return ArrayHelper::getValueOrSelf((array) ArrayHelper::get($this->config, 'account_types', []), $accountType);
    }
    
    
    /**
     * 获取系统配置键名称
     * @param string $accountType 账户类型
     * @return string
     */
    public function getSettingKey(string $accountType) : string
    {
        return 'plugin_verify_code_' . $accountType;
    }
    
    
    /**
     * 获取验证码配置
     * @param string     $accountType 账户类型
     * @param string     $name 配置名
     * @param mixed|null $default 默认值
     * @return mixed
     */
    public function getCodeConfig(string $accountType, string $name, mixed $default = null) : mixed
    {
        return ArrayHelper::get(
            array_merge(
                (array) $this->getAccountTypeMap($accountType),
                SystemConfig::init()->getSettingData($this->getSettingKey($accountType))
            ),
            $name,
            $default
        );
    }
    
    
    /**
     * 获取验证码有效时长
     * @param string $accountType 账户类型
     * @return int
     */
    public function getCodeExpire(string $accountType) : int
    {
        $expire = (int) $this->getCodeConfig($accountType, 'expire', 0);
        
        return $expire ?: 600;
    }
    
    
    /**
     * 获取可重发验证码时长
     * @param string $accountType 账户类型
     * @return int
     */
    public function getCodeRepeat(string $accountType) : int
    {
        $repeat = (int) $this->getCodeConfig($accountType, 'repeat', 0);
        
        return $repeat ?: 60;
    }
    
    
    /**
     * 获取验证码样式
     * @param string   $accountType 账户类型
     * @param int|null $style
     * @return int
     */
    public function getCodeStyle(string $accountType, int $style = null) : int
    {
        if (null === $style) {
            $style = (int) $this->getCodeConfig($accountType, 'style', self::STYLE_NUMBER);
        }
        
        if (!self::getStyleMap($style)) {
            return self::STYLE_NUMBER;
        }
        
        return $style;
    }
    
    
    /**
     * 获取验证码长度
     * @param string $accountType
     * @return int
     */
    public function getCodeLength(string $accountType) : int
    {
        $repeat = (int) $this->getCodeConfig($accountType, 'length', 0);
        
        return $repeat ?: 6;
    }
    
    
    /**
     * 生成验证码
     * @param string   $accountType 账号类型
     * @param string   $account 账号
     * @param string   $codeType 验证码类型
     * @param int|null $style 验证码样式
     * @return string
     * @throws DataNotFoundException
     * @throws DbException
     */
    public function create(string $accountType, string $account, string $codeType = '', int $style = null) : string
    {
        $data              = PluginVerifyCodeField::init();
        $data->accountType = trim($accountType);
        $data->account     = trim($account);
        $data->codeType    = trim($codeType);
        $data->id          = self::buildId($data->accountType, $data->account, $data->codeType);
        
        
        if ($info = $this->findInfo($data->id)) {
            // 检测是否可重发
            $repeatTime  = $info->createTime + $this->getCodeRepeat($data->accountType);
            $currentTime = time();
            if ($repeatTime > $currentTime) {
                $surplus = $repeatTime - $currentTime;
                throw new VerifyException("请于{$surplus}秒后重试", 'later_send');
            }
        }
        
        $data->code = StringHelper::random(
            $this->getCodeLength($data->accountType),
            $this->getCodeStyle($data->accountType, $style)
        );
        $data->ip   = Request::ip();
        $this->replace()->insert($data);
        
        return $data->code;
    }
    
    
    /**
     * 清理验证码
     * @param string $accountType 账号类型
     * @param string $account 账号
     * @param string $codeType 验证码类型
     * @throws DbException
     */
    public function clear(string $accountType, string $account = '', string $codeType = '')
    {
        $accountType = trim($accountType);
        $account     = trim($account);
        $codeType    = trim($codeType);
        
        if ($account) {
            $this->delete(self::buildId($accountType, $account, $codeType));
        }
        
        // 清理过期验证码
        $this->where(PluginVerifyCodeField::createTime('<=', time() - $this->getCodeExpire($accountType)))
            ->delete();
    }
    
    
    /**
     * 校验验证码
     * @param string $accountType 账号类型
     * @param string $account 账号
     * @param string $code 验证码
     * @param string $codeType 验证码类型
     * @param bool   $clear 验证完成后是否清理验证码
     * @throws DataNotFoundException
     * @throws DbException
     */
    public function check(string $accountType, string $account, string $code, string $codeType = '', bool $clear = true)
    {
        $accountType = trim($accountType);
        $account     = trim($account);
        $codeType    = trim($codeType);
        $code        = trim($code);
        
        if (!$info = $this->findInfo(self::buildId($accountType, $account, $codeType))) {
            throw new VerifyException('验证码不正确', 'need_send');
        }
        
        if ($info->createTime + $this->getCodeExpire($accountType) <= time()) {
            if ($clear) {
                $this->clear($accountType, $account, $codeType);
            }
            
            throw new VerifyException('验证码已过期', 'expired');
        }
        
        if ($info->code !== $code) {
            throw new VerifyException('验证码不正确', 'bad');
        }
        
        if ($clear) {
            $this->clear($accountType, $account, $codeType);
        }
    }
    
    
    /**
     * 生成ID
     * @param string $accountType
     * @param string $account
     * @param string $codeType
     * @return string
     */
    public static function buildId(string $accountType, string $account, string $codeType = '') : string
    {
        return md5("{$accountType}_{$account}_$codeType");
    }
}