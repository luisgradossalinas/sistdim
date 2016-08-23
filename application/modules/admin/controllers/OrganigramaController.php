<?php

class Admin_OrganigramaController extends App_Controller_Action_Admin
{
    
    private $_organoModel;
    private $_organoForm;
    private $_uorganicaModel;
    private $_uorganicaForm;
    
    const INACTIVO = 0;
    const ACTIVO = 1;
    const ELIMINADO = 2;
    
    public function init()
    {
        $this->_organoModel = new Application_Model_Organo;
        $this->_organoForm = new Application_Form_Organo;
        $this->_uorganicaModel = new Application_Model_UnidadOrganica;
        $this->_uorganicaForm = new Application_Form_UnidadOrganica;
        
        $this->view->headScript()->appendFile(SITE_URL . '/js/web/organigrama.js');
        Zend_Layout::getMvcInstance()->assign('show','1'); //No mostrar en el menú la barra horizontal
        parent::init();
        
    }
    
    public function indexAction() {
        
        Zend_Layout::getMvcInstance()->assign('active', 'Organigrama');
        Zend_Layout::getMvcInstance()->assign('padre', 3);
        Zend_Layout::getMvcInstance()->assign('link', 'organigrama');
        
        $sesion_usuario = new Zend_Session_Namespace('sesion_usuario');
        $proyecto = $sesion_usuario->sesion_usuario['id_proyecto'];
        
        $this->view->organo = $this->_organoModel->obtenerOrgano($proyecto);
        $this->view->unidad = $this->_uorganicaModel->obtenerUOrganica($proyecto);
        
    }
    
    public function operacionAction () 
    {
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $sesionMvc  = new Zend_Session_Namespace('sesion_mvc');
        $sesion_usuario = new Zend_Session_Namespace('sesion_usuario');
        $proyecto = $sesion_usuario->sesion_usuario['id_proyecto'];
        $data = $this->_getAllParams();
        
        //Previene vulnerabilidad XSS (Cross-site scripting)
        $filtro = new Zend_Filter_StripTags();
        foreach ($data as $key => $val) {
            $data[$key] = $filtro->filter(trim($val));
        }
        
        $tipo = $data['tipo'];
        $modelo = "this_".$tipo."Model";
        $form = "this_".$tipo."Form";
        
        if ($this->_getParam('ajax') == 'form') {
            
            if ($this->_hasParam('id')) {
                
                $id = $this->_getParam('id');
                if ($id != 0) {
                    $data = ${$modelo}->fetchRow('id_organo = '.$id);
                    ${$form}->populate($data->toArray());
                }
            }
            //$this->_organoForm->getElement('email')->setAttrib('readonly','readonly');   
            //$this->_organoForm->removeElement('unidad_organica');
            echo ${$form};         
        }
        
        if ($this->_getParam('ajax') == 'validar') {
                echo ${$form}->processAjax($data);
        }
        
        if ($this->_getParam('ajax') == 'delete') {
            $where = $this->getAdapter()->quoteInto('id_organigrama = ?',$data['id']);
            ${$modelo}->update(array('estado' => self::ELIMINADO),$where);
            
            $sesionMvc->messages = 'Registro eliminado';
            $sesionMvc->tipoMessages = self::SUCCESS;
                    
        }
        
        if ($this->_getParam('ajax') == 'save') {
      
            if ($this->_getParam('scrud') == 'nuevo') {
                $data['fecha_crea'] = date("Y-m-d H:i:s");
                $data['usuario_crea'] = Zend_Auth::getInstance()->getIdentity()->id;
                $data['id_proyecto'] = $proyecto;
                $sesionMvc->messages = 'Registro agregado satisfactoriamente';
            } else {
                $data['fecha_actu'] = date("Y-m-d H:i:s");
                $data['usuario_actu'] = Zend_Auth::getInstance()->getIdentity()->id;
                $data['id_proyecto'] = $proyecto;
                $sesionMvc->messages = 'Registro actualizado satisfactoriamente';
            }
            
            $sesionMvc->tipoMessages = self::SUCCESS;
            
            ${$modelo}->guardar($data);
        }
        
    }
    
    
    public function registroAction() {
        
        Zend_Layout::getMvcInstance()->assign('active', 'Registrar puestos');
        Zend_Layout::getMvcInstance()->assign('padre', 3);
        Zend_Layout::getMvcInstance()->assign('link', 'regpuestos');
        
    }
    

}



