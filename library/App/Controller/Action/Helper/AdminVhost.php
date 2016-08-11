<?php

class App_Controller_Action_Helper_AdminVhost extends Zend_Controller_Action_Helper_Abstract
{
    // @codingStandardsIgnoreStart
    protected $allowed = array(
        'auth/login'
    );
    // @codingStandardsIgnoreEnd

    public function preDispatch()
    {
        parent::preDispatch();
        $config = Zend_Registry::get('config');

        $shouldIUseAdmVhost = $config->get('useExclusiveVhostForAdmin', false);
        $iAmUsingAdvVhost = $config->app->adminUrl == 'http://' . $_SERVER['SERVER_NAME'];
        $iAmOnAdmMod = $this->getRequest()->getModuleName() == 'admin';
        $isAuth = $this->getRequest()->getControllerName() == 'auth';

        if ($shouldIUseAdmVhost && !$isAuth ) {
            if ($iAmOnAdmMod && !$iAmUsingAdvVhost) {
                header('Location: '.$config->app->adminUrl);
            }

            if ($iAmUsingAdvVhost && !$iAmOnAdmMod) {
                $r = new Zend_Controller_Action_Helper_Redirector();
                $r->gotoUrl('/admin');
            }
            
            if ($iAmUsingAdvVhost && $iAmOnAdmMod) {
                $this->getActionController()->view->headScript()->appendScript('urls.siteUrl=urls.adminUrl;');
            }
        }
    }
}