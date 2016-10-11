<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    public function _initConfig()
    {
        $config = new Zend_Config($this->getOptions(), true);
        $config->merge(new Zend_Config_Ini(APPLICATION_PATH.'/configs/servir.ini'));
        $config->merge(new Zend_Config_Ini(APPLICATION_PATH.'/configs/security.ini'));
        //$config->setReadOnly();
        Zend_Registry::set('config', $config);
    }
    
    public function _initViewHelpers() 
    {
        $doctypeHelper = new Zend_View_Helper_Doctype();
        $doctypeHelper->doctype(Zend_View_Helper_Doctype::HTML5);
        $this->bootstrap('layout');
        $layout = $this->getResource('layout');
        $view = $layout->getView();       
        $config = Zend_Registry::get('config');
        $view->headMeta()->appendHttpEquiv('Content-Type', 'text/html; charset=utf-8');
        $view->headMeta()->appendName("robots", "noindex, nofollow x");

        $js = sprintf(
            "var urls = {
                siteUrl : '%s',
                redondeo : %s
            }", 
            $config->app->siteUrl,$config->valor->redondeo
        );

        $view->headScript()->appendScript($js);
       
        define('SITE_URL', $config->app->siteUrl);
        define('TITLE', $config->app->title);
        
    }
    
    protected function __initSession() 
    {
        Zend_Session::start();
    }
    
    protected function _initDbResource() 
    {
        
        $this->_executeResource('db');
        $adapter = $this->getResource('db');
        Zend_Registry::set('db', $adapter);
        
    }
    
    protected function _initServir() {
        date_default_timezone_set('America/Lima');
        Zend_Locale::setDefault('es');
    }
    
    public function _initLibrerias()
    {
        require_once( APPLICATION_PATH . "/../library/Word/PHPWord.php");
        require_once( APPLICATION_PATH . "/../library/Excel/PHPExcel.php");

    }
    
    
    //Configuración de SEO
    protected function _initRoutes() 
    {
        $routeConfig = new Zend_Config_Ini(APPLICATION_PATH . '/configs/routes.ini');
        $router = new Zend_Controller_Router_Rewrite();
        $router->addConfig($routeConfig);
        $this->getResource('frontController')->setRouter($router);
    }
    
    /*public function _initPlugins()
    {
        $this->bootstrap('frontcontroller');
        $frontController = $this->getResource('frontcontroller');

        $plugin = new App_Plugin_SetupApplication();
        $frontController->registerPlugin($plugin);
    }*/

}

