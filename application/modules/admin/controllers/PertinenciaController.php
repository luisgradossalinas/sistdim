<?php

class Admin_PertinenciaController extends App_Controller_Action_Admin {

    private $_organoModel;
    private $_actividad;

    public function init() {

        $this->_organoModel = new Application_Model_Organo;
        $this->_actividad = new Application_Model_Actividad;

        $sesion_usuario = new Zend_Session_Namespace('sesion_usuario');
        $this->_proyecto = $sesion_usuario->sesion_usuario['id_proyecto'];
        $this->_usuario = $sesion_usuario->sesion_usuario['id'];
        $this->_mapaPuesto = $sesion_usuario->sesion_usuario['mapa_puesto'];
        
        $this->view->headScript()->appendFile(SITE_URL . '/js/web/pertinencia.js');
        Zend_Layout::getMvcInstance()->assign('show', '1'); //No mostrar en el menú la barra horizontal
        parent::init();
    }

    public function indexAction() {

        Zend_Layout::getMvcInstance()->assign('active', 'Análisis de pertinencia');
        Zend_Layout::getMvcInstance()->assign('padre', 6);
        Zend_Layout::getMvcInstance()->assign('link', 'pertinencia');
         
        $this->view->organo = $this->_organoModel->obtenerOrgano($this->_proyecto);
        
    }
    
    public function obtenerActividadPuestoAction() {
        
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

        if ($this->_hasParam('puesto')) {
            $puesto = $this->_getParam('puesto');
            $dataAct = $this->_actividad->obtenerActividadPuesto($puesto);
            $contador = 0;
            
            //Obtener nivel y caegoria del puesto
            foreach ($dataAct as $value) {
                $dataAct[$contador]['nivel_puesto'] = $this->getHelper('nivelpuesto')->select($value['codigo_grupo'], $value['id_nivel_puesto'], $contador + 1);
                $dataAct[$contador]['categoria_puesto'] = $this->getHelper('categoriapuesto')->select($value['codigo_familia'], $value['id_categoria_puesto'], $contador + 1);
                $contador++;
            }

            echo Zend_Json::encode($dataAct);
        }

    }

}
