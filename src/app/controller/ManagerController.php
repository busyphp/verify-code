<?php

namespace BusyPHP\verifycode\app\controller;

use BusyPHP\app\admin\model\system\menu\SystemMenu;
use BusyPHP\app\admin\model\system\menu\SystemMenuField;
use BusyPHP\app\admin\model\system\plugin\SystemPlugin;
use BusyPHP\contract\abstracts\PluginManager;
use Exception;
use RuntimeException;
use think\Response;

/**
 * 插件管理
 * @author busy^life <busy.life@qq.com>
 * @copyright (c) 2015--2021 ShanXi Han Tuo Technology Co.,Ltd. All rights reserved.
 * @version $Id: 2021/11/4 下午2:11 ManagerController.php $
 */
class ManagerController extends PluginManager
{
    /**
     * 创建表SQL
     * @var string[]
     */
    private $createTableSql = [
        'verify_code' => "CREATE TABLE `#__table_prefix__#plugin_verify_code` (
    `id` VARCHAR(32) NOT NULL COMMENT 'ID',
    `code` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '验证码',
    `code_type` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '验证码类型',
    `create_time` INT(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
    `account_type` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '账号类型',
    `account` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '账号',
    `ip` VARCHAR(45) NOT NULL DEFAULT '' COMMENT 'IP',
    PRIMARY KEY (`id`),
    KEY `create_time` (`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='验证码验证表'",
    ];
    
    /**
     * 删除表SQL
     * @var string[]
     */
    private $deleteTableSql = [
        "DROP TABLE IF EXISTS `#__table_prefix__#plugin_verify_code`",
    ];
    
    
    /**
     * 返回模板路径
     * @return string
     */
    protected function viewPath() : string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR;
    }
    
    
    /**
     * 安装插件
     * @return Response
     * @throws Exception
     */
    public function install() : Response
    {
        $menuModel = SystemMenu::init();
        $model     = SystemPlugin::init();
        $model->startTrans();
        try {
            $settingPath = 'plugins_verify_code/setting';
            
            // 不存在设置菜单则创建
            if (!$menuModel->whereEntity(SystemMenuField::path($settingPath))->count()) {
                $menuModel->addMenu($settingPath, '消息验证码设置', '#system_setting', 'fa fa-envelope-square', false);
            }
            
            foreach ($this->deleteTableSql as $item) {
                $this->executeSQL($item);
            }
            
            foreach ($this->createTableSql as $item) {
                $this->executeSQL($item);
            }
            
            $model->setInstall($this->info->package);
            
            $model->commit();
        } catch (Exception $e) {
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
     * @throws Exception
     */
    public function uninstall() : Response
    {
        $menuModel = SystemMenu::init();
        $model     = SystemPlugin::init();
        $model->startTrans();
        try {
            $menuModel->deleteByPath('plugins_verify_code/setting', true);
            
            foreach ($this->deleteTableSql as $item) {
                $this->executeSQL($item);
            }
            
            $model->setUninstall($this->info->package);
            
            $model->commit();
        } catch (Exception $e) {
            $model->rollback();
            
            throw $e;
        }
        
        $this->updateCache();
        $this->logUninstall();
        
        return $this->success('卸载成功');
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