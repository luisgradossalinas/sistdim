<?php

class Admin_ProcesosController extends App_Controller_Action_Admin
{
    
    private $_proceso0;
    private $_proceso1;
    private $_proceso2;
    private $_proceso3;
    private $_proceso4;
    
    
    public function init()
    {
        
        $this->_proceso0 = new Application_Model_Proceso0;
        $this->_proceso1 = new Application_Model_Proceso1;
        $this->_proceso2 = new Application_Model_Proceso2;
        $this->_proceso3 = new Application_Model_Proceso3;
        $this->_proceso4 = new Application_Model_Proceso4;
        
        parent::init();
        Zend_Layout::getMvcInstance()->assign('show', '1'); //No mostrar en el menú la barra horizontal

    }
    
    public function indexAction() {
        
        Zend_Layout::getMvcInstance()->assign('active', 'Registrar inventario');
        Zend_Layout::getMvcInstance()->assign('padre', 4);
        Zend_Layout::getMvcInstance()->assign('link', 'inventario');
        
        $this->view->headScript()->appendFile(SITE_URL . '/js/web/inventario.js');
        
    }
    
    public function registroActiAction() {
        
        Zend_Layout::getMvcInstance()->assign('active', 'Registrar actividades y tareas');
        Zend_Layout::getMvcInstance()->assign('padre', 4);
        Zend_Layout::getMvcInstance()->assign('link', 'registroacti');
        
    }
    

}



