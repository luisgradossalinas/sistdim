<?php

class Admin_DotacionController extends App_Controller_Action_Admin {

    private $_organoModel;
    private $_actividad;
    private $_tarea;
    
    private $_usuario;

    public function init() {

        $this->_organoModel = new Application_Model_Organo;
        $this->_actividad = new Application_Model_Actividad;
        $this->_tarea = new Application_Model_Tarea;

        $sesion_usuario = new Zend_Session_Namespace('sesion_usuario');
        $this->_proyecto = $sesion_usuario->sesion_usuario['id_proyecto'];
        $this->_usuario = $sesion_usuario->sesion_usuario['id'];

        $this->view->headScript()->appendFile(SITE_URL . '/js/web/dotacion.js');
        Zend_Layout::getMvcInstance()->assign('show', '1'); //No mostrar en el menú la barra horizontal
        parent::init();
    }

    public function indexAction() {

        Zend_Layout::getMvcInstance()->assign('active', 'Registrar tiempos y frecuencias');
        Zend_Layout::getMvcInstance()->assign('padre', 7);
        Zend_Layout::getMvcInstance()->assign('link', 'dotacion');

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
                    $dataActividad = array('id_actividad' => $idAct, 'id_nivel_puesto' => $add[3],
                        'id_categoria_puesto' => $add[4],'nombre_puesto' => $add[5],'usuario_actu' => $this->_usuario, 'fecha_actu' => date("Y-m-d H:i:s"));
                    $this->_actividad->guardar($dataActividad);
                } else { // Es tarea
                    $dataTarea = array('id_tarea' => $idTarea, 'id_nivel_puesto' => $add[3],
                        'id_categoria_puesto' => $add[4],'nombre_puesto' => $add[5],'usuario_actu' => $this->_usuario, 'fecha_actu' => date("Y-m-d H:i:s"));
                    $this->_tarea->guardar($dataTarea);
                }
            }
        }
        echo Zend_Json::encode('Pertinencia grabada satisfactoriamente.');
    }

}
