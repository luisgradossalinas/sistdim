<?php

class App_Controller_Action_Admin extends App_Controller_Action
{
    
    public function init()
    {
        
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            $this->_redirect('/login');
        }
        
        $sesion_usuario = new Zend_Session_Namespace('sesion_usuario');
        
        Zend_Layout::getMvcInstance()->assign('user', $sesion_usuario->sesion_usuario['nombre_completo']);
        Zend_Layout::getMvcInstance()->assign('rol', $sesion_usuario->sesion_usuario['nombre_rol']);
        Zend_Layout::getMvcInstance()->assign('css', $this->getConfig()->app->estiloCss);
        
        $rol = $sesion_usuario->sesion_usuario['id_rol'];
        $url = substr($_SERVER['REQUEST_URI'],1);
        if (!$this->getRequest()->isXmlHttpRequest()) {
            if ($url != self::MODULO_ADMIN) {
                $recursoModelo = new Application_Model_Recurso;
                $acceso = ($recursoModelo->validaAcceso($rol, $url));

                if ($acceso == self::ACCESO_DENEGADO)
                    exit("No tiene permiso para acceder a este recurso<a href='javascript:history.back()'>Volver</a>");
            }
        }
    }
}