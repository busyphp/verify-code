<?php

namespace BusyPHP\verifycode\app\controller;

use BusyPHP\app\admin\controller\AdminController;
use BusyPHP\app\admin\model\system\plugin\SystemPlugin;
use BusyPHP\helper\FilterHelper;
use BusyPHP\verifycode\model\VerifyCodeConfig;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\Response;

/**
 * 验证码设置
 * @author busy^life <busy.life@qq.com>
 * @copyright (c) 2015--2021 ShanXi Han Tuo Technology Co.,Ltd. All rights reserved.
 * @version $Id: 2021/11/4 下午2:12 IndexController.php $
 */
class IndexController extends AdminController
{
    use VerifyCodeConfig;
    
    /**
     * 设置
     * @return Response
     * @throws DataNotFoundException
     * @throws DbException
     */
    public function setting()
    {
        $accounts = $this->getVerifyCodeConfig('accounts', []);
        
        if ($this->isPost()) {
            $data             = $this->post('data/a');
            $data             = FilterHelper::trim($data);
            $data['accounts'] = $data['accounts'] ?? [];
            
            foreach ($accounts as $type => $item) {
                $data['accounts'][$type]['expire'] = $data['accounts'][$type]['expire'] === '' ? null : intval($data['accounts'][$type]['expire']);
                $data['accounts'][$type]['repeat'] = $data['accounts'][$type]['repeat'] === '' ? null : intval($data['accounts'][$type]['repeat']);
                $data['accounts'][$type]['length'] = $data['accounts'][$type]['length'] === '' ? null : intval($data['accounts'][$type]['length']);
                $data['accounts'][$type]['shape']  = $data['accounts'][$type]['shape'] === '' ? null : intval($data['accounts'][$type]['shape']);
            }
            
            SystemPlugin::init()->setSetting('busyphp/verify-code', $data);
            
            $this->log()->record(self::LOG_DEFAULT, '消息验证码设置');
            
            return $this->success('设置成功');
        }
        
        $this->assign('accounts', $accounts);
        $this->assign('info', SystemPlugin::init()->getSetting('busyphp/verify-code'));
        
        return $this->display();
    }
    
    
    protected function display($template = '', $charset = 'utf-8', $contentType = '', $content = '')
    {
        $dir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'plugins_verify_code' . DIRECTORY_SEPARATOR;
        if ($template) {
            $template = $dir . $template . '.html';
        } else {
            $template = $dir . $this->request->action() . '.html';
        }
        
        return parent::display($template, $charset, $contentType, $content);
    }
}