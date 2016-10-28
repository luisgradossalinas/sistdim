<?php

class Admin_PertinenciaController extends App_Controller_Action_Admin {

    private $_organoModel;
    private $_actividad;
    private $_tarea;
    private $_puesto;
    private $_usuario;
    private $_proyecto;

    public function init() {

        $this->_organoModel = new Application_Model_Organo;
        $this->_actividad = new Application_Model_Actividad;
        $this->_tarea = new Application_Model_Tarea;
        $this->_puesto = new Application_Model_Puesto;

        $sesion_usuario = new Zend_Session_Namespace('sesion_usuario');
        $this->_proyecto = $sesion_usuario->sesion_usuario['id_proyecto'];
        $this->_usuario = $sesion_usuario->sesion_usuario['id'];

        $this->view->headScript()->appendFile(SITE_URL . '/js/web/pertinencia.js');
        Zend_Layout::getMvcInstance()->assign('show', '1'); //No mostrar en el menú la barra horizontal
        parent::init();
    }

    public function indexAction() {

        Zend_Layout::getMvcInstance()->assign('active', 'Análisis de pertinencia');
        Zend_Layout::getMvcInstance()->assign('padre', 7);
        Zend_Layout::getMvcInstance()->assign('link', 'pertinencia');

        $this->view->organo = $this->_organoModel->obtenerOrgano($this->_proyecto);
    }

    //Invocada por AJAX obtiene todas las actividades y tareas que tiene un ejecutor
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
            $dataAct = $this->_actividad->obtenerActividadTareaPuesto($puesto); //Incluye tareas
            $contador = 0;

            //Obtener nivel y caegoria del puesto
            foreach ($dataAct as $value) {
                //Si es actividad asignarle tarea vacía
                $dataAct[$contador]['nivel_puesto'] = $this->getHelper('nivelpuesto')->select($value['codigo_grupo'], $value['id_nivel_puesto'], $contador + 1);
                $dataAct[$contador]['categoria_puesto'] = $this->getHelper('categoriapuesto')->select($value['codigo_familia'], $value['id_categoria_puesto'], $contador + 1);
                if ($dataAct[$contador]['tarea'] == "0") {
                    $dataAct[$contador]['tarea'] = '';
                }

                $dataAct[$contador]['grupo'] = $this->getHelper('grupo')->select($value['codigo_grupo'], $contador + 1);
                $dataAct[$contador]['familia'] = $this->getHelper('familia')->select($value['codigo_grupo'], $value['codigo_familia'], $contador + 1);
                $dataAct[$contador]['rpuesto'] = $this->getHelper('rolpuesto')->select($value['codigo_familia'], $value['codigo_rol_puesto'], $contador + 1);

                $contador++;
            }
            echo Zend_Json::encode($dataAct);
        }
    }

    public function grabarPertinenciaAction() {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $data = $this->_getAllParams();

        if (!$this->getRequest()->isXmlHttpRequest())
            exit('Acción solo válida para peticiones ajax');

        $pertinencia = isset($data['pertinencia']) ? $data['pertinencia'] : array();

        if (count($pertinencia) > 0) {
            foreach ($pertinencia as $reg) {
                //Validar si es tarea o actividad -- Todo es actualizar (nivel_puesto,categoria_puesto y 

                $add = explode("|", $reg);
                $idAct = $add[0];
                $idTarea = $add[1];

                if ($idTarea == 0) { //Es actividad
                    $dataActividad = array('id_actividad' => $idAct, 'id_nivel_puesto' => $add[6],
                        'codigo_grupo' => $add[3], 'codigo_familia' => $add[4], 'codigo_rol_puesto' => $add[5],
                        'id_categoria_puesto' => $add[7], 'nombre_puesto' => $add[8], 
                        'usuario_actu_pertinencia' => $this->_usuario, 'fecha_actu_pertinencia' => date("Y-m-d H:i:s"));
                    $this->_actividad->guardar($dataActividad);
                } else { // Es tarea
                    $dataTarea = array('id_tarea' => $idTarea, 'id_nivel_puesto' => $add[6],
                        'codigo_grupo' => $add[3], 'codigo_familia' => $add[4], 'codigo_rol_puesto' => $add[5],
                        'id_categoria_puesto' => $add[7], 'nombre_puesto' => $add[8], 
                        'usuario_actu_pertinencia' => $this->_usuario, 'fecha_actu_pertinencia' => date("Y-m-d H:i:s"));
                    $this->_tarea->guardar($dataTarea);
                }
            }
        }
        echo Zend_Json::encode('Pertinencia grabada satisfactoriamente.');
    }

    public function obtenerPuestosAction() {

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
        $dataPuesto = $this->_puesto->obtenerPuestoPertinencia($unidad);
        echo Zend_Json::encode($dataPuesto);
    }

}
