<?php

class Admin_ProyectoController extends App_Controller_Action_Admin
{
    
	private $_usuarioModel;
	private $_recursoModel;
	private $_proyectoModel;

    public function init()
    {
        parent::init();

        $this->_usuarioModel = new Application_Model_Usuario;
        $this->_recursoModel = new Application_Model_Recurso;
        $this->_proyectoModel = new Application_Model_Proyecto;

    }
    
    public function proyectoUsuarioAction() {
        
        $this->view->headScript()->appendFile(SITE_URL.'/js/web/proyecto-usuario.js');

        Zend_Layout::getMvcInstance()->assign('active', 'Proyecto / Usuario');
        Zend_Layout::getMvcInstance()->assign('padre', 2);
        Zend_Layout::getMvcInstance()->assign('link', 'proyusuario');
        
    	$this->view->usuarios = $this->_usuarioModel->usuariosActivos();
    	$this->view->proyecto = $this->_proyectoModel->proyectosActivos();
        
    }

    public function permisoUsuarioAction() {

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

        
        if ($this->_hasParam('usuario')) {
            $usuario = $this->_getParam('usuario');
            $proyecto = $this->_getParam('proyecto');
            $listadoProy = $this->_recursoModel->recursosUsuario($usuario,$proyecto);
            echo Zend_Json::encode($listadoProy);
            
        }
        






    }

    

}



