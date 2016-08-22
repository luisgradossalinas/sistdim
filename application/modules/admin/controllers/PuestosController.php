<?php

class Admin_PuestosController extends App_Controller_Action_Admin
{
    
    private $_organigramaModel;
    private $_formOrganigrama;
    
    public function init()
    {
        $this->_organigramaModel = new Application_Model_Organigrama;
        $this->_formOrganigrama = new Application_Form_Organigrama;
        
        $this->view->headScript()->appendFile(SITE_URL . '/js/web/organigrama.js');
        
        parent::init();
        
    }
    
    public function organigramaAction() {
        
        Zend_Layout::getMvcInstance()->assign('active', 'Organigrama');
        Zend_Layout::getMvcInstance()->assign('padre', 3);
        Zend_Layout::getMvcInstance()->assign('link', 'organigrama');
        
        $sesion_usuario = new Zend_Session_Namespace('sesion_usuario');
        $proyecto = $sesion_usuario->sesion_usuario['id_proyecto'];
        
        $this->view->data = $this->_organigramaModel->obtenerNaturalezaOrgano($proyecto);
        
    }
    
    public function organoAction () 
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
        
        if ($this->_getParam('ajax') == 'form') {
            
            if ($this->_hasParam('id')) {
                
                $id = $this->_getParam('id');
                if ($id != 0) {
                    //$data = $this->_clase->fetchRow(''.$primaryKey.' = '.$id);
                    $this->_formOrganigrama->populate($data->toArray());
                }
            }
            //$this->_formOrganigrama->getElement('email')->setAttrib('readonly','readonly');   
            $this->_formOrganigrama->removeElement('unidad_organica');
            echo $this->_formOrganigrama;         
        }
        
        if ($this->_getParam('ajax') == 'validar') {
                echo $this->_formOrganigrama->processAjax($data);
        }
        
        if ($this->_getParam('ajax') == 'delete') {
            $where = $this->getAdapter()->quoteInto('id_organigrama = ?',$data['id']);
            $this->_organigramaModel->update(array('estado' => self::ELIMINADO),$where);
            
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
            
            $this->_organigramaModel->guardar($data);
        }
        
    }
    
    
    public function registroAction() {
        
        Zend_Layout::getMvcInstance()->assign('active', 'Registrar puestos');
        Zend_Layout::getMvcInstance()->assign('padre', 3);
        Zend_Layout::getMvcInstance()->assign('link', 'regpuestos');
        
    }
    

}



