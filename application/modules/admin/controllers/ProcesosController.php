<?php

class Admin_ProcesosController extends App_Controller_Action_Admin
{
    
    public function init()
    {
        parent::init();

    }
    
    public function inventarioAction() {
        
        Zend_Layout::getMvcInstance()->assign('active', 'Registrar inventario');
        Zend_Layout::getMvcInstance()->assign('padre', 4);
        Zend_Layout::getMvcInstance()->assign('link', 'inventario');
        
    }
    
    public function registroActiAction() {
        
        Zend_Layout::getMvcInstance()->assign('active', 'Registrar actividades y tareas');
        Zend_Layout::getMvcInstance()->assign('padre', 4);
        Zend_Layout::getMvcInstance()->assign('link', 'registroacti');
        
    }
    

}



