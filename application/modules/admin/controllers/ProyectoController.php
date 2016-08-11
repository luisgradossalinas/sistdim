<?php

class Admin_ProyectoController extends App_Controller_Action_Admin
{
    
    public function init()
    {
        parent::init();

    }
    
    public function proyectoUsuarioAction() {
        
        Zend_Layout::getMvcInstance()->assign('active', 'Proyecto / Usuario');
        Zend_Layout::getMvcInstance()->assign('padre', 2);
        Zend_Layout::getMvcInstance()->assign('link', 'proyusuario');
        
    }

    

}



