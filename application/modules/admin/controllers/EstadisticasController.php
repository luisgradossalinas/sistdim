<?php

class Admin_EstadisticasController extends App_Controller_Action_Admin
{
    
    const INACTIVO = 0;
    const ACTIVO = 1;
    const ELIMINADO = 2;
    
    private $_rolrecurso;
    private $_recurso;
    
    public function init()
    {
        parent::init();
        $this->view->headScript()->appendFile(SITE_URL.'/js/plugins/jquery.jqChart.js');
        
    }
    
    public function usuarioAction() 
    {
        Zend_Layout::getMvcInstance()->assign('active', 'estusuarios');
        Zend_Layout::getMvcInstance()->assign('padre', '6');
        Zend_Layout::getMvcInstance()->assign('link', 'estusuarios');

    }
    
    public function productoAction() 
    {
        Zend_Layout::getMvcInstance()->assign('active', 'estproductos');
        Zend_Layout::getMvcInstance()->assign('padre', '6');
        Zend_Layout::getMvcInstance()->assign('link', 'estproductos');
        $this->view->headScript()->appendFile(SITE_URL.'/js/estadisticas/producto.js');

    }
    

}



