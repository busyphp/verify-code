<?php
declare(strict_types = 1);

namespace BusyPHP\verifycode\app\controller;

use BusyPHP\app\admin\controller\develop\plugin\SystemPluginBaseController;
use BusyPHP\app\admin\model\system\plugin\SystemPlugin;
use Exception;
use RuntimeException;
use think\Response;
use Throwable;

/**
 * 插件管理
 * @author busy^life <busy.life@qq.com>
 * @copyright (c) 2015--2023 ShanXi Han Tuo Technology Co.,Ltd. All rights reserved.
 * @version $Id: 2023/10/11 12:35 PluginController.php $
 */
class PluginController extends SystemPluginBaseController
{
    /**
     * 创建表SQL
     * @var string
     */
    private string $createTableSql = <<<SQL
CREATE TABLE `#__table_prefix__#plugin_verify_code` (
    `id` VARCHAR(32) NOT NULL COMMENT 'ID',
    `code` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '验证码',
    `code_type` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '验证码类型',
    `create_time` INT(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
    `account_type` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '账号类型',
    `account` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '账号',
    `ip` VARCHAR(45) NOT NULL DEFAULT '' COMMENT 'IP',
    PRIMARY KEY (`id`),
    KEY `create_time` (`create_time`),
    KEY `code_type` (`code_type`),
    KEY `account_type` (`account_type`),
    KEY `account` (`account`),
    KEY `ip` (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='验证码验证表'
SQL;
    
    
    /**
     * 返回模板路径
     * @return string
     */
    protected function viewPath() : string
    {
        return '';
    }
    
    
    /**
     * 安装插件
     * @return Response
     * @throws Throwable
     */
    public function install() : Response
    {
        $model = SystemPlugin::init();
        $model->startTrans();
        try {
            if ($this->hasTable('plugin_verify_code')) {
                throw new RuntimeException('plugin_verify_code数据表已存在');
            }
            
            $this->executeSQL($this->createTableSql);
            $model->setInstall($this->info->package);
            $model->commit();
        } catch (Throwable $e) {
            $model->rollback();
            
            throw $e;
        }
        
        $this->updateCache();
        $this->logInstall();
        
        return $this->success('安装成功');
    }
    
    
    /**
     * 卸载插件
     * @return Response
     * @throws Throwable
     */
    public function uninstall() : Response
    {
        throw new RuntimeException('不支持卸载');
    }
    
    
    /**
     * 设置插件
     * @return Response
     * @return Exception
     */
    public function setting() : Response
    {
        throw new RuntimeException('不支持设置');
    }
}