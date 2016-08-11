<?php

/**
 * Config Aplicación
 *
 * @author Martin Grados
 */
class App_Plugin_SetupApplication extends Zend_Controller_Plugin_Abstract
{
    //put your code here

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $url = $_SERVER['SERVER_NAME'];
        $config = Zend_Registry::get('config');

      
    }
}
