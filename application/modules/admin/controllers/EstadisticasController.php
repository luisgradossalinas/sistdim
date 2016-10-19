<?php

class Admin_EstadisticasController extends App_Controller_Action_Admin {

    const INACTIVO = 0;
    const ACTIVO = 1;
    const ELIMINADO = 2;

    private $_organo;
    private $_puesto;
    private $_proyecto;

    public function init() {
        parent::init();
        $this->_organo = new Application_Model_Organo;
        $this->_puesto = new Application_Model_Puesto;

        $sesion_usuario = new Zend_Session_Namespace('sesion_usuario');
        $this->_proyecto = $sesion_usuario->sesion_usuario['id_proyecto'];

        $this->view->headScript()->appendFile(SITE_URL . '/js/plugins/jquery.jqChart.js');
        Zend_Layout::getMvcInstance()->assign('show', '1'); //No mostrar en el menú la barra horizontal
    }

    public function usuarioAction() {
        Zend_Layout::getMvcInstance()->assign('active', 'estusuarios');
        Zend_Layout::getMvcInstance()->assign('padre', '6');
        Zend_Layout::getMvcInstance()->assign('link', 'estusuarios');
    }

    public function productoAction() {
        Zend_Layout::getMvcInstance()->assign('active', 'estproductos');
        Zend_Layout::getMvcInstance()->assign('padre', '6');
        Zend_Layout::getMvcInstance()->assign('link', 'estproductos');
        $this->view->headScript()->appendFile(SITE_URL . '/js/estadisticas/producto.js');
    }

    public function puestoUnidadAction() {
        Zend_Layout::getMvcInstance()->assign('active', 'Puestos por unidad orgánica (Con dotación)');
        Zend_Layout::getMvcInstance()->assign('padre', '9');
        Zend_Layout::getMvcInstance()->assign('link', 'est_punidad');
        $this->view->headScript()->appendFile(SITE_URL . '/js/estadisticas/puesto-unidad.js');

        $this->view->organo = $this->_organo->obtenerOrgano($this->_proyecto);
    }

    public function puestosDotacionAction() {

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

        $unidad = $data['unidad'];
        $dataPuesto = $this->_puesto->puestosDotacion($unidad);

        $dataGrafico = array();
        foreach ($dataPuesto as $value) {
            $dataGrafico[] = array($value['puesto'], (float)$value['dotacion']);
        }

        echo Zend_Json::encode($dataGrafico);
    }

}
