<?php

class Admin_DotacionController extends App_Controller_Action_Admin {

    private $_organoModel;
    private $_actividad;
    private $_tarea;
    private $_puesto;
    
    private $_usuario;
    private $_rol;

    public function init() {

        $this->_organoModel = new Application_Model_Organo;
        $this->_actividad = new Application_Model_Actividad;
        $this->_tarea = new Application_Model_Tarea;
        $this->_puesto = new Application_Model_Puesto;

        $sesion_usuario = new Zend_Session_Namespace('sesion_usuario');
        $this->_proyecto = $sesion_usuario->sesion_usuario['id_proyecto'];
        $this->_usuario = $sesion_usuario->sesion_usuario['id'];
        $this->_rol = $sesion_usuario->sesion_usuario['id_rol'];
        
        Zend_Layout::getMvcInstance()->assign('show', '1'); //No mostrar en el menú la barra horizontal
        parent::init();
    }

    public function indexAction() {

        Zend_Layout::getMvcInstance()->assign('active', 'Registrar tiempos y frecuencias');
        Zend_Layout::getMvcInstance()->assign('padre', 7);
        Zend_Layout::getMvcInstance()->assign('link', 'dotacion');
        $this->view->headScript()->appendFile(SITE_URL . '/js/web/dotacion.js');

        $this->view->organo = $this->_organoModel->obtenerOrgano($this->_proyecto);
        $this->view->rol = $this->_rol;
        
    }

    public function obtenerDotacionAction() {

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
            $dataAct = $this->_actividad->obtenerActividadTareaDotacion($puesto); //Incluye tareas
            $contador = 0;

            foreach ($dataAct as $value) {
                
                $niveles = $this->_actividad->obtenerNombreNiveles($value['nivel'],$value['id_proceso']);
                
                //Si es actividad asignarle tarea vacía
                $dataAct[$contador]['periodicidad'] = $this->getHelper('periodicidad')->select($value['id_periodicidad'], $contador + 1);
                $dataAct[$contador]['tiempo'] = $this->getHelper('tiempo')->select($value['id_tiempo'], $contador + 1);
                if ($dataAct[$contador]['tarea'] == "0") {
                    $dataAct[$contador]['tarea'] = '';
                }
                if ($dataAct[$contador]['periodicidad'] == "0") {
                    $dataAct[$contador]['periodicidad'] = '';
                }
                if ($dataAct[$contador]['tiempo'] == "0") {
                    $dataAct[$contador]['tiempo'] = '';
                }
                if ($dataAct[$contador]['frecuencia'] == "0.00") {
                    $dataAct[$contador]['frecuencia'] = '';
                }
                if ($dataAct[$contador]['duracion'] == "0.00") {
                    $dataAct[$contador]['duracion'] = '';
                }
                
                $dataAct[$contador]['nivel0'] = $niveles['nivel0'];
                $dataAct[$contador]['nivel1'] = $niveles['nivel1'];
                $dataAct[$contador]['nivel2'] = $niveles['nivel2'];
                $dataAct[$contador]['nivel3'] = $niveles['nivel3'];
                $dataAct[$contador]['nivel4'] = $niveles['nivel4'];
                $dataPuesto = $this->_puesto->fetchRow('id_puesto = '.$puesto)->toArray();
                $dataAct[$contador]['total_dotacion'] = $dataPuesto['total_dotacion'];
                $contador++;
            }

            echo Zend_Json::encode($dataAct);
        }
    }

    public function grabarDotacionAction() {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $data = $this->_getAllParams();

        if (!$this->getRequest()->isXmlHttpRequest())
            exit('Acción solo válida para peticiones ajax');

        $dotacion = isset($data['dotacion']) ? $data['dotacion'] : array();

        if (count($dotacion) > 0) {
            foreach ($dotacion as $reg) {
                //Validar si es tarea o actividad -- Todo es actualizar (nivel_puesto,categoria_puesto y 
                $add = explode("|", $reg);
                $idAct = $add[0];
                $idTarea = $add[1];

                if ($idTarea == 0) { //Es actividad
                    $dataActividad = array('id_actividad' => $idAct, 'id_periodicidad' => $add[2],'frecuencia' => $add[3],
                        'id_tiempo' => $add[4],'duracion' => $add[5],'usuario_actu_dotacion' => $this->_usuario, 'fecha_actu_dotacion' => date("Y-m-d H:i:s"));
                    $this->_actividad->guardar($dataActividad);
                } else { // Es tarea
                    $dataTarea = array('id_tarea' => $idTarea, 'id_periodicidad' => $add[2],'frecuencia' => $add[3],
                        'id_tiempo' => $add[4],'duracion' => $add[5],'usuario_actu_dotacion' => $this->_usuario, 'fecha_actu_dotacion' => date("Y-m-d H:i:s"));
                    $this->_tarea->guardar($dataTarea);
                }
            }
            $totalDotacion = $this->_getParam('totalDotacion');
            $puesto = $this->_getParam('puesto');
            $nomTrabajador = $this->_getParam('nombre_trabajador');
            //Actualizar dotación del puesto

            //Requerimiento 5
            //Validar si es invitado solo actualizar el nombre_trabajador
            if (!empty($nomTrabajador)) { //Invitado
                $dataPuesto = array('id_puesto' => $puesto, 'total_dotacion' => $totalDotacion, 'nombre_trabajador' => $nomTrabajador,
                'usuario_actu_dotacion' => $this->_usuario, 'fecha_actu_dotacion' => date("Y-m-d H:i:s"));
            } else {
                $dataPuesto = array('id_puesto' => $puesto, 'total_dotacion' => $totalDotacion,
                'usuario_actu_dotacion' => $this->_usuario, 'fecha_actu_dotacion' => date("Y-m-d H:i:s"));
            }
            
            $this->_puesto->guardar($dataPuesto);
        }
        $data['msg'] = 'Dotación grabada satisfactoriamente.';
        $data['rol'] =  $this->_rol;
        echo Zend_Json::encode($data);
    }
    
    public function avanceAction() {
        
        Zend_Layout::getMvcInstance()->assign('active', 'Avance de dotación');
        Zend_Layout::getMvcInstance()->assign('padre', 7);
        Zend_Layout::getMvcInstance()->assign('link', 'avance');
        
        $this->view->headScript()->appendFile(SITE_URL . '/js/web/avance.js');
        $this->view->organo = $this->_organoModel->obtenerOrgano($this->_proyecto);
        
    }
}
