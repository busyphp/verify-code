<?php
declare(strict_types = 1);

namespace BusyPHP\verifycode\app\controller;

use BusyPHP\app\admin\annotation\MenuNode;
use BusyPHP\app\admin\annotation\MenuRoute;
use BusyPHP\app\admin\component\js\driver\Table;
use BusyPHP\app\admin\controller\AdminController;
use BusyPHP\app\admin\model\system\config\SystemConfig;
use BusyPHP\app\admin\model\system\config\SystemConfigField;
use BusyPHP\app\admin\model\system\menu\SystemMenu;
use BusyPHP\helper\FilterHelper;
use BusyPHP\verifycode\model\PluginVerifyCode;
use think\db\exception\DataNotFoundException;
use think\Response;
use Throwable;

/**
 * 验证码设置
 * @author busy^life <busy.life@qq.com>
 * @copyright (c) 2015--2021 ShanXi Han Tuo Technology Co.,Ltd. All rights reserved.
 * @version $Id: 2021/11/4 下午2:12 SettingController.php $
 */
#[MenuRoute(path: 'plugin_verify_code', class: true)]
class SettingController extends AdminController
{
    protected function display($template = '', $charset = 'utf-8', $contentType = '', $content = '') : Response
    {
        $this->app->config->set([
            'view_path'   => __DIR__ . '/../view/',
            'view_depr'   => DIRECTORY_SEPARATOR,
            'view_suffix' => 'html',
            'auto_rule'   => 1
        ], 'view');
        
        return parent::display($template, $charset, $contentType, $content);
    }
    
    
    /**
     * @return Response
     * @throws Throwable
     */
    #[MenuNode(menu: false, name: '消息验证码', parent: 'system_manager/index', sort: 200)]
    public function index() : Response
    {
        if ($table = Table::initIfRequest()) {
            $list  = [];
            $model = PluginVerifyCode::init();
            foreach ($model->getAccountTypeMap() as $id => $item) {
                $name   = $item['name'] ?? '';
                $list[] = [
                    'id'     => $id,
                    'name'   => $name ?: $id,
                    'length' => $model->getCodeLength($id),
                    'repeat' => $model->getCodeRepeat($id),
                    'expire' => $model->getCodeExpire($id),
                    'style'  => $model::getStyleMap($model->getCodeStyle($id)),
                ];
            }
            
            return $table->list($list)->response();
        }
        
        $this->assign('nav', SystemMenu::init()->getChildList('system_manager/index', true, true));
        
        return $this->display();
    }
    
    
    /**
     * @return Response
     * @throws DataNotFoundException
     * @throws Throwable
     */
    #[MenuNode(menu: false, name: '配置消息验证码参数', parent: '/index')]
    public function setting() : Response
    {
        $model      = PluginVerifyCode::init();
        $id         = $this->param('id/s', 'trim');
        $settingKey = $model->getSettingKey($id);
        
        if ($this->isPost()) {
            $data = SystemConfigField::init();
            $data->setSystem(true);
            $data->setName(sprintf('消息验证码 - %s', $id));
            $data->setContent(FilterHelper::trim($this->post('data/a')));
            SystemConfig::init()->setting($settingKey, $data);
            
            $this->log()->record(self::LOG_UPDATE, '配置消息验证码参数');
            
            return $this->success('配置成功');
        }
        
        $name          = $model->getAccountTypeMap($id)['name'] ?? '';
        $info          = SystemConfig::instance()->getSettingData($settingKey);
        $info['style'] = $info['style'] ?? $model->getCodeStyle($id);
        
        $this->assign('id', $id);
        $this->assign('name', $name ?: $id);
        $this->assign('info', $info);
        
        return $this->display();
    }
}