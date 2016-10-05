<?php

class Admin_ReportesController extends App_Controller_Action_Admin {

    private $_puesto;
    private $_organo;
    private $_unidadOrganica;
    private $_usuario;
    private $_rol;
    private $_proyecto;

    public function init() {
        $this->_puesto = new Application_Model_Puesto;
        $this->_organo = new Application_Model_Organo;
        $this->_unidadOrganica = new Application_Model_UnidadOrganica;

        $sesion_usuario = new Zend_Session_Namespace('sesion_usuario');
        $this->_proyecto = $sesion_usuario->sesion_usuario['id_proyecto'];
        $this->_usuario = $sesion_usuario->sesion_usuario['id'];
        $this->_rol = $sesion_usuario->sesion_usuario['id_rol'];

        Zend_Layout::getMvcInstance()->assign('show', '1'); //No mostrar en el menú la barra horizontal
        parent::init();
    }

    public function organoUnidadAction() {
        Zend_Layout::getMvcInstance()->assign('active', 'Por Órgano / Unidad Orgánica');
        Zend_Layout::getMvcInstance()->assign('padre', 8);
        Zend_Layout::getMvcInstance()->assign('link', 'reporteorganounidad');

        $this->view->headScript()->appendFile(SITE_URL . '/js/reportes/organo-unidad.js');
        $this->view->organo = $this->_organo->obtenerOrgano($this->_proyecto);
    }

    public function exportWordOrganoUnidadAction() {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $data = $this->_getAllParams();
        //Previene vulnerabilidad XSS (Cross-site scripting)
        $filtro = new Zend_Filter_StripTags();
        foreach ($data as $key => $val) {
            $data[$key] = $filtro->filter(trim($val));
        }
        
        //echo $this->getConfig()->text->uni;

        if (!$this->getRequest()->isXmlHttpRequest())
            exit('Acción solo válida para peticiones ajax');

        if ($this->_hasParam('unidad')) {
            $unidad = $this->_getParam('unidad');
            $dataPuesto = $this->_puesto->obtenerPuestos($unidad);
            $valorServir = (int)$this->getConfig()->valor->redondeo;
            $contador = 0;
            $tcant = 0;
            $tdota = 0;
            $dataWord = array();
            foreach ($dataPuesto as $value) {
                
                $dataWord[$contador]['puesto'] = $value['puesto'];
                $dataWord[$contador]['cantidad'] = $value['cantidad'];
                
                $tdotacion = explode('.', round($value['total_dotacion'],2));
                if ((int)@$tdotacion[1] >= $valorServir) {
                    $tdotacion = (int)$tdotacion[0] + 1;
                } else {
                    $tdotacion = (int)$tdotacion[0];
                }
                
                $tcant += $value['cantidad'];
                $tdota += $tdotacion;
                
                $dataWord[$contador]['tdota'] = $tdotacion;
                $dataWord[$contador]['necesidades'] = $tdotacion - $value['cantidad'];
                $contador++;
            }
            
            $dataWord[$contador]['puesto'] = 'Total';
            $dataWord[$contador]['cantidad'] = $tcant;
            $dataWord[$contador]['tdota'] = $tdota;
            $dataWord[$contador]['necesidades'] = $tdota - $tcant;
            
            print_r($dataWord);
            
        }
    }

    public function grupoFamiliaRolAction() {
        Zend_Layout::getMvcInstance()->assign('active', 'Por Grupo, Familia y Rol');
        Zend_Layout::getMvcInstance()->assign('padre', 8);
        Zend_Layout::getMvcInstance()->assign('link', 'gfrol');

        $this->view->headScript()->appendFile(SITE_URL . '/js/reportes/grupo-familia-rol.js');
        $this->view->organo = $this->_organo->obtenerOrgano($this->_proyecto);
    }

    public function estadoProyectoAction() {
        Zend_Layout::getMvcInstance()->assign('active', 'Estado del proyecto');
        Zend_Layout::getMvcInstance()->assign('padre', 8);
        Zend_Layout::getMvcInstance()->assign('link', 'estproy');

        $this->view->headScript()->appendFile(SITE_URL . '/js/reportes/estado-proyecto.js');
        $this->view->organoUnidad = $this->_unidadOrganica->obtenerOrganoUOrganica($this->_proyecto);
    }

    public function analisisPertinenciaAction() {
        Zend_Layout::getMvcInstance()->assign('active', 'Reporte análisis de pertinencia');
        Zend_Layout::getMvcInstance()->assign('padre', 8);
        Zend_Layout::getMvcInstance()->assign('link', 'analpert');

        $this->view->headScript()->appendFile(SITE_URL . '/js/reportes/analisis-pertinencia.js');
        $this->view->organo = $this->_organo->obtenerOrgano($this->_proyecto);
    }

    public function dimensionamientoAction() {
        Zend_Layout::getMvcInstance()->assign('active', 'Matriz de dimensionamiento');
        Zend_Layout::getMvcInstance()->assign('padre', 8);
        Zend_Layout::getMvcInstance()->assign('link', 'dimensionamiento');
    }

}
