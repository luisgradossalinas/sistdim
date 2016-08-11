<?php

class Admin_IndexController extends App_Controller_Action_Admin
{
    private $_usuarioModel = null;
    private $_productoModel = null;
    private $_categoriaModel = null;
    private $_usuarioForm = null;

    public function init()
    {
        parent::init();
        $this->_usuarioModel = new Application_Model_Usuario;
        $this->_productoModel = new Application_Model_Producto;
        $this->_categoriaModel = new Application_Model_Categoria;
        $this->_usuarioForm = new Application_Form_Usuario;
    }

    public function indexAction()
    {
        $this->_helper->layout->setLayout("admin");

        $sesion_usuario = new Zend_Session_Namespace('sesion_usuario');

        $this->view->active = 'Recursos para el usuario 
            ' . $sesion_usuario->sesion_usuario['nombre_completo'].
                " (".$sesion_usuario->sesion_usuario['nombre_rol'].")";

        $id = $sesion_usuario->sesion_usuario['id'];


       // $this->view->idrol = $sesion_usuario->sesion_usuario['id_rol'];

        $this->view->data = $this->_usuarioModel->recursosPorUsuario($id);
        Zend_Layout::getMvcInstance()->assign('btnNuevo','0');
        Zend_Layout::getMvcInstance()->assign('idrol',$sesion_usuario->sesion_usuario['id_rol']);
    }
        public function plantillaAction()
    {
        $this->_helper->setLayout('plantilla');
    }

    public function usuarioAction()
    {
        Zend_Layout::getMvcInstance()->assign('active','usuarios');
        $this->view->headLink()->appendStylesheet(SITE_URL.'/jquery/css/dataTables.css', 'all');
        $this->view->headScript()->appendFile(SITE_URL.'/jquery/plugins/jquery.dataTables.js');
        $this->view->headScript()->appendFile(SITE_URL.'/assets/js/bootstrap-dataTable.js');
        $this->view->headScript()->appendFile(SITE_URL.'/assets/web/usuario.js');
        $data = $this->_usuarioModel->fetchAll();
        $this->view->usuario = $data;
    }
    
    public function changeCssAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        
        
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->getConfig()->app->estiloCss = $this->_getParam('color');
        }
        echo $this->getConfig()->app->estiloCss;
        /*
        $config = Zend_Registry::get('config');
        $config->app->estiloCss = $this->_getParam('estilo');
        //echo ESTILO_CSS;
        //echo $config->app->estiloCss;exit;
        //!defined('SITE_URL')? define('SITE_URL', $config->app->siteUrl):null; 
        //echo ESTILO_CSS;exit;
        $this->_redirect(SITE_URL);*/
    }
    
    public function testAction() {
        
        
        echo "hola";
        
    }

    public function misDatosAction() {
        
        Zend_Layout::getMvcInstance()->assign('padre', 1);
        Zend_Layout::getMvcInstance()->assign('link', 'misdatos');

        $sesion_usuario = new Zend_Session_Namespace('sesion_usuario');
        $idUsuario = $sesion_usuario->sesion_usuario['id'];

        $this->view->data = $this->_usuarioModel->usuarioActual($idUsuario);
        
    }

    public function operacionAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $generator = new Generator_Modelo;
        $sesionMvc  = new Zend_Session_Namespace('sesion_mvc');
        $primaryKey = $generator->getPrimaryKey('usuario');
        $data = $this->_getAllParams();
        

        //Previene vulnerabilidad XSS (Cross-site scripting)
        $filtro = new Zend_Filter_StripTags();
        foreach ($data as $key => $val) {
            $data[$key] = $filtro->filter(trim($val));
        }
        
        if ($this->_getParam('ajax') == 'form') {
            
            if ($this->_hasParam('id')) {
                
                $id = $this->_getParam('id');
                if ($id != 0) {
                    $data = $this->_usuarioModel->fetchRow("id = ".$id);
                    $this->_usuarioForm->getElement('email')->setAttrib('readonly','readonly');   
                    $this->_usuarioForm->removeElement('id_rol');
                    $this->_usuarioForm->removeElement('estado');
                    $this->_usuarioForm->populate($data->toArray());
                }
            }
            echo $this->_usuarioForm;         
        }
        
        if ($this->_getParam('ajax') == 'validar') {
                echo $this->_usuarioForm->processAjax($data);
        }
        
        if ($this->_getParam('ajax') == 'delete') {
            
            $where = $this->getAdapter()->quoteInto('id = ?',$data['id']);
            $this->_usuarioModel->update(array('estado' => self::ELIMINADO),$where);
            
            $sesionMvc->messages = 'Registro eliminado';
            $sesionMvc->tipoMessages = self::SUCCESS;
                    
        }
        
        if ($this->_getParam('ajax') == 'save') {
      
            if ($this->_getParam('scrud') == 'nuevo') {
                $data['fecha_crea'] = date("Y-m-d H:i:s");
                $data['usuario_crea'] = Zend_Auth::getInstance()->getIdentity()->id;
                $sesionMvc->messages = 'Registro agregado satisfactoriamente';
            } else {
                $data['fecha_actu'] = date("Y-m-d H:i:s");
                $data['usuario_actu'] = Zend_Auth::getInstance()->getIdentity()->id;
                $sesionMvc->messages = 'Registro actualizado satisfactoriamente';
            }
            
            $sesionMvc->tipoMessages = self::SUCCESS;
            
            $this->_usuarioModel->guardar($data);
        }
    }


}





