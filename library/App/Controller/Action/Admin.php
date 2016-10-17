<?php

class App_Controller_Action_Admin extends App_Controller_Action {

    public function init() {

        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            $this->_redirect('/login');
        }

        $sesion_usuario = new Zend_Session_Namespace('sesion_usuario');

        Zend_Layout::getMvcInstance()->assign('user', $sesion_usuario->sesion_usuario['nombre_completo']);
        Zend_Layout::getMvcInstance()->assign('rol', $sesion_usuario->sesion_usuario['nombre_rol']);
        Zend_Layout::getMvcInstance()->assign('id_usuario', $sesion_usuario->sesion_usuario['id']);
        Zend_Layout::getMvcInstance()->assign('id_rol', $sesion_usuario->sesion_usuario['id_rol']);
        Zend_Layout::getMvcInstance()->assign('id_proyecto', $sesion_usuario->sesion_usuario['id_proyecto']);
        Zend_Layout::getMvcInstance()->assign('nombre_proyecto', $sesion_usuario->sesion_usuario['nombre_proyecto']);
        Zend_Layout::getMvcInstance()->assign('css', $this->getConfig()->app->estiloCss);

        $rol = $sesion_usuario->sesion_usuario['id_rol'];
        $url = substr($_SERVER['REQUEST_URI'], 1);


        $valUrl = explode("/", $url);
        if (count($valUrl) == 3) {
            $valUrl = $valUrl[0] . "/" . $valUrl[1] . "/" . $valUrl[2];
        } else {
            $valUrl = '';
        }

        $dataUrl = $this->getConfig()->accesos->url->toArray();



        if (!$this->getRequest()->isXmlHttpRequest() && !in_array($valUrl, $dataUrl)) {
            //if (!$this->getRequest()->isXmlHttpRequest()) {
            if ($url != self::MODULO_ADMIN) {
                $recursoModelo = new Application_Model_Recurso;
                $acceso = ($recursoModelo->validaAcceso($rol, $url));

                if ($acceso == self::ACCESO_DENEGADO)
                    exit("No tiene permiso para acceder a este recurso<a href='javascript:history.back()'>Volver</a>");
            }
        }
    }

}
