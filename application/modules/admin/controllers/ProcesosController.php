<?php

class Admin_ProcesosController extends App_Controller_Action_Admin
{
    
    private $_proceso0;
    private $_proceso1;
    private $_proceso2;
    private $_proceso3;
    private $_proceso4;
    
    private $_usuario;
    private $_proyecto;
    
    public function init()
    {
        
        $this->_proceso0 = new Application_Model_Proceso0;
        $this->_proceso1 = new Application_Model_Proceso1;
        $this->_proceso2 = new Application_Model_Proceso2;
        $this->_proceso3 = new Application_Model_Proceso3;
        $this->_proceso4 = new Application_Model_Proceso4;
        
        $sesion_usuario = new Zend_Session_Namespace('sesion_usuario');
        $this->_proyecto = $sesion_usuario->sesion_usuario['id_proyecto'];
        $this->_usuario = $sesion_usuario->sesion_usuario['id'];
        
        parent::init();
        Zend_Layout::getMvcInstance()->assign('show', '1'); //No mostrar en el menú la barra horizontal

    }
    
    public function indexAction() {
        
        Zend_Layout::getMvcInstance()->assign('active', 'Registrar inventario');
        Zend_Layout::getMvcInstance()->assign('padre', 4);
        Zend_Layout::getMvcInstance()->assign('link', 'inventario');
        
        $this->view->headScript()->appendFile(SITE_URL . '/js/web/inventario.js');
        
        $this->view->proceso0 = $this->_proceso0->combo($this->_proyecto);
        $this->view->proceso1 = $this->_proceso1->combo($this->_proyecto);
        $this->view->proceso2 = $this->_proceso2->combo($this->_proyecto);
        $this->view->proceso3 = $this->_proceso3->combo($this->_proyecto);
        $this->view->proceso4 = $this->_proceso4->combo($this->_proyecto);
    }
    
    public function obtenerProcesos1Action() {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $data = $this->_getAllParams();
        //Previene vulnerabilidad XSS (Cross-site scripting)
        $filtro = new Zend_Filter_StripTags();
        foreach ($data as $key => $val) {
            $data[$key] = $filtro->filter(trim($val));
        }

        if (!$this->getRequest()->isXmlHttpRequest())
            exit('Acción solo válida para peticiones ajax');

        if ($this->_hasParam('n0')) {
            $n0 = $this->_getParam('n0');
            $dataProceso1 = $this->_proceso1->obtenerProcesos1($n0);
            echo Zend_Json::encode($dataProceso1);
        }
    }
    
    public function obtenerProcesos2Action() {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $data = $this->_getAllParams();
        //Previene vulnerabilidad XSS (Cross-site scripting)
        $filtro = new Zend_Filter_StripTags();
        foreach ($data as $key => $val) {
            $data[$key] = $filtro->filter(trim($val));
        }

        if (!$this->getRequest()->isXmlHttpRequest())
            exit('Acción solo válida para peticiones ajax');

        if ($this->_hasParam('n1')) {
            $n1 = $this->_getParam('n1');
            $dataProceso2 = $this->_proceso2->obtenerProcesos2($n1);
            echo Zend_Json::encode($dataProceso2);
        }
    }
    
    public function obtenerProcesos3Action() {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $data = $this->_getAllParams();
        //Previene vulnerabilidad XSS (Cross-site scripting)
        $filtro = new Zend_Filter_StripTags();
        foreach ($data as $key => $val) {
            $data[$key] = $filtro->filter(trim($val));
        }

        if (!$this->getRequest()->isXmlHttpRequest())
            exit('Acción solo válida para peticiones ajax');

        if ($this->_hasParam('n2')) {
            $n2 = $this->_getParam('n2');
            $dataProceso3 = $this->_proceso3->obtenerProcesos3($n2);
            echo Zend_Json::encode($dataProceso3);
        }
    }
    
    public function obtenerProcesos4Action() {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $data = $this->_getAllParams();
        //Previene vulnerabilidad XSS (Cross-site scripting)
        $filtro = new Zend_Filter_StripTags();
        foreach ($data as $key => $val) {
            $data[$key] = $filtro->filter(trim($val));
        }

        if (!$this->getRequest()->isXmlHttpRequest())
            exit('Acción solo válida para peticiones ajax');

        if ($this->_hasParam('n3')) {
            $n3 = $this->_getParam('n3');
            $dataProceso4 = $this->_proceso4->obtenerProcesos4($n3);
            echo Zend_Json::encode($dataProceso4);
        }
    }
    
    
    
    public function registroActiAction() {
        
        Zend_Layout::getMvcInstance()->assign('active', 'Registrar actividades y tareas');
        Zend_Layout::getMvcInstance()->assign('padre', 4);
        Zend_Layout::getMvcInstance()->assign('link', 'registroacti');
        
    }
    

}



