<?php

class Admin_ProyectoController extends App_Controller_Action_Admin {

    private $_usuarioModel;
    private $_recursoModel;
    private $_proyectoModel;

    public function init() {
        parent::init();

        $this->_usuarioModel = new Application_Model_Usuario;
        $this->_recursoModel = new Application_Model_Recurso;
        $this->_proyectoModel = new Application_Model_Proyecto;
    }

    public function proyectoUsuarioAction() {

        $this->view->headScript()->appendFile(SITE_URL . '/js/web/proyecto-usuario.js');

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
            $listadoProy = $this->_recursoModel->recursosUsuario($usuario, $proyecto);
            echo Zend_Json::encode($listadoProy);
        }
    }

    public function grabarPermisosAction() {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if (!$this->getRequest()->isXmlHttpRequest())
            exit('Acción solo válida para peticiones ajax');

        $usuario = $this->_getParam('usuario');
        $proyecto = $this->_getParam('proyecto');
        $recursoADD = $this->_getParam('rec_add');
        $recursoDEL = $this->_getParam('rec_del');
        
        $dataUsuario = $this->_usuarioModel->fetchRow("id = " .$usuario)->toArray();
        $id_rol = $dataUsuario['id_rol'];
        
        //Validar si hay data
        $rolrecurso = new Application_Model_RolRecurso();
        $valida = $rolrecurso->validaRecursoUsuario($usuario, $proyecto);
        
        //Primera vez
        if(empty($valida)) {
            if (count($recursoADD) > 0) {
                foreach ($recursoADD as $reg) {
                        $rolrecurso->insert(
                        array('id_rol' => $id_rol,
                        'id_recurso' => $reg,
                        'id_proyecto' => $proyecto,
                        'id_usuario' => $usuario,
                        'estado' => 1));
                }
            }
            
            if (count($recursoDEL) > 0) {
                foreach ($recursoDEL as $reg) {
                        $rolrecurso->insert(
                        array('id_rol' => $id_rol,
                        'id_recurso' => $reg,
                        'id_proyecto' => $proyecto,
                        'id_usuario' => $usuario,
                        'estado' => 0));
                }
            }
            
        } else {
            //Actualizar permisos
            if (count($recursoADD) > 0) {
                $add = implode(",", $recursoADD);
                $sqlADD = 'id_recurso in ('.$add.') and id_rol = '.$id_rol.
                        ' and id_proyecto = '.$proyecto.' and id_usuario = '.$usuario;
                $rolrecurso->update(array('estado' => 1),$sqlADD);
            }
            if (count($recursoDEL) > 0) {
                $del = implode(",", $recursoDEL);
                $sqlDEL = 'id_recurso in ('.$del.') and id_rol = '.$id_rol.
                        ' and id_proyecto = '.$proyecto.' and id_usuario = '.$usuario;
                $rolrecurso->update(array('estado' => 0),$sqlDEL);
            }
        }
        
        $sesionMvc->messages = 'Permisos actualizados';
        $sesionMvc->tipoMessages = self::SUCCESS;
        
            
    }

}
