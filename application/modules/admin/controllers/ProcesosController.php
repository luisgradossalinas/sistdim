<?php

class Admin_ProcesosController extends App_Controller_Action_Admin {

    private $_proceso0;
    private $_proceso1;
    private $_proceso2;
    private $_proceso3;
    private $_proceso4;
    private $_usuario;
    private $_actividad;
    private $_tipoProceso;
    private $_unidad;
    private $_puesto;
    private $_tarea;
    private $_proyecto;

    public function init() {

        $this->_proceso0 = new Application_Model_Proceso0;
        $this->_proceso1 = new Application_Model_Proceso1;
        $this->_proceso2 = new Application_Model_Proceso2;
        $this->_proceso3 = new Application_Model_Proceso3;
        $this->_proceso4 = new Application_Model_Proceso4;
        $this->_actividad = new Application_Model_Actividad;
        $this->_tipoProceso = new Application_Model_Tipoproceso;
        $this->_unidad = new Application_Model_UnidadOrganica;
        $this->_puesto = new Application_Model_Puesto;
        $this->_tarea = new Application_Model_Tarea;

        $sesion_usuario = new Zend_Session_Namespace('sesion_usuario');
        $this->_proyecto = $sesion_usuario->sesion_usuario['id_proyecto'];
        $this->_usuario = $sesion_usuario->sesion_usuario['id'];

        parent::init();
        Zend_Layout::getMvcInstance()->assign('show', '1'); //No mostrar en el menú la barra horizontal
    }

    public function indexAction() {

        Zend_Layout::getMvcInstance()->assign('active', 'Registrar inventario');
        Zend_Layout::getMvcInstance()->assign('padre', 4);
        Zend_Layout::getMvcInstance()->assign('link', 'inventario');

        $this->view->headScript()->appendFile(SITE_URL . '/js/web/inventario.js');

        $this->view->proceso0 = $this->_proceso0->combo($this->_proyecto);
        $this->view->proceso1 = $this->_proceso1->combo($this->_proyecto);
        $this->view->proceso2 = $this->_proceso2->combo($this->_proyecto);
        $this->view->proceso3 = $this->_proceso3->combo($this->_proyecto);
    }

    public function obtenerProcesos0Action() {

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

        $dataProceso0 = $this->_proceso0->obtenerProcesos0($this->_proyecto);
        $contador = 0;
        foreach ($dataProceso0 as $value) {
            $dataProceso0[$contador]['codigo_tipoproceso'] = $this->getHelper('tipoproceso')->select($value['codigo_tipoproceso'], $contador + 1);
            $contador++;
        }
        echo Zend_Json::encode($dataProceso0);
    }

    public function obtenerProcesos1Action() {

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

        if ($this->_hasParam('n0')) {
            $n0 = $this->_getParam('n0');
            $dataProceso1 = $this->_proceso1->obtenerProcesos1($n0);
            echo Zend_Json::encode($dataProceso1);
        }
    }

    public function obtenerProcesos2Action() {

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

        if ($this->_hasParam('n1')) {
            $n1 = $this->_getParam('n1');
            $dataProceso2 = $this->_proceso2->obtenerProcesos2($n1);
            echo Zend_Json::encode($dataProceso2);
        }
    }

    public function obtenerProcesos3Action() {

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

        if ($this->_hasParam('n2')) {
            $n2 = $this->_getParam('n2');
            $dataProceso3 = $this->_proceso3->obtenerProcesos3($n2);
            echo Zend_Json::encode($dataProceso3);
        }
    }

    public function obtenerProcesos4Action() {

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

        if ($this->_hasParam('n3')) {
            $n3 = $this->_getParam('n3');
            $dataProceso4 = $this->_proceso4->obtenerProcesos4($n3);
            echo Zend_Json::encode($dataProceso4);
        }
    }

    //obtener-tipo-proceso
    public function obtenerTipoProcesoAction() {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        if (!$this->getRequest()->isXmlHttpRequest())
            exit('Acción solo válida para peticiones ajax');

        $dataTipoProceso = $this->_tipoProceso->listado();
        echo Zend_Json::encode($dataTipoProceso);
    }

    public function grabarProcesosAction() {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $data = $this->_getAllParams();
        $nivel = $data['n']; //Identifica que nivel de proceso se va a guardar
        $modelo = '';
        if ($nivel == 0) {
            $modelo = $this->_proceso0;
        } else if ($nivel == 1) {
            $modelo = $this->_proceso1;
            $flag = $this->_proceso0;
        } else if ($nivel == 2) {
            $modelo = $this->_proceso2;
            $flag = $this->_proceso1;
        } else if ($nivel == 3) {
            $modelo = $this->_proceso3;
            $flag = $this->_proceso2;
        } else if ($nivel == 4) {
            $modelo = $this->_proceso4;
            $flag = $this->_proceso3;
        }

        if (!$this->getRequest()->isXmlHttpRequest())
            exit('Acción solo válida para peticiones ajax');

        $procesos = isset($data['procesos']) ? $data['procesos'] : array();
        if (count($procesos) > 0) {
            foreach ($procesos as $reg) {

                $add = explode("|", $reg);
                $proceso = $add[1];
                if ($add[0] == 0) { //Nuevo
                    if ($nivel == 0) {
                        $dataProceso = array('id_proceso_n0' => $add[0], 'descripcion' => $add[2], 'codigo_tipoproceso' => $add[1],
                            'id_proyecto' => $this->_proyecto, 'usuario_crea' => $this->_usuario, 'fecha_crea' => date("Y-m-d H:i:s"));
                    } else if ($nivel == 1) {
                        $dataProceso = array('id_proceso_n1' => $add[0], 'id_proceso_n0' => $add[1], 'descripcion' => $add[2],
                            'id_proyecto' => $this->_proyecto, 'usuario_crea' => $this->_usuario, 'fecha_crea' => date("Y-m-d H:i:s"));
                    } else if ($nivel == 2) {
                        $dataProceso = array('id_proceso_n2' => $add[0], 'id_proceso_n1' => $add[1], 'descripcion' => $add[2],
                            'id_proyecto' => $this->_proyecto, 'usuario_crea' => $this->_usuario, 'fecha_crea' => date("Y-m-d H:i:s"));
                    } else if ($nivel == 3) {
                        $dataProceso = array('id_proceso_n3' => $add[0], 'id_proceso_n2' => $add[1], 'descripcion' => $add[2],
                            'id_proyecto' => $this->_proyecto, 'usuario_crea' => $this->_usuario, 'fecha_crea' => date("Y-m-d H:i:s"));
                    } else if ($nivel == 4) {
                        $dataProceso = array('id_proceso_n4' => $add[0], 'id_proceso_n3' => $add[1], 'descripcion' => $add[2],
                            'id_proyecto' => $this->_proyecto, 'usuario_crea' => $this->_usuario, 'fecha_crea' => date("Y-m-d H:i:s"));
                    }
                } else {
                    if ($nivel == 0) {
                        $dataProceso = array('id_proceso_n0' => $add[0], 'descripcion' => $add[2], 'codigo_tipoproceso' => $add[1],
                            'usuario_actu' => $this->_usuario, 'fecha_actu' => date("Y-m-d H:i:s"));
                    } else if ($nivel == 1) {
                        $dataProceso = array('id_proceso_n1' => $add[0], 'id_proceso_n0' => $add[1], 'descripcion' => $add[2],
                            'usuario_actu' => $this->_usuario, 'fecha_actu' => date("Y-m-d H:i:s"));
                    } else if ($nivel == 2) {
                        $dataProceso = array('id_proceso_n2' => $add[0], 'id_proceso_n1' => $add[1], 'descripcion' => $add[2],
                            'usuario_actu' => $this->_usuario, 'fecha_actu' => date("Y-m-d H:i:s"));
                    } else if ($nivel == 3) {
                        $dataProceso = array('id_proceso_n3' => $add[0], 'id_proceso_n2' => $add[1], 'descripcion' => $add[2],
                            'usuario_actu' => $this->_usuario, 'fecha_actu' => date("Y-m-d H:i:s"));
                    } else if ($nivel == 4) {
                        $dataProceso = array('id_proceso_n4' => $add[0], 'id_proceso_n3' => $add[1], 'descripcion' => $add[2],
                            'usuario_actu' => $this->_usuario, 'fecha_actu' => date("Y-m-d H:i:s"));
                    }
                }
                $modelo->guardar($dataProceso);
            }
            //Actualizar flag para indicar que tiene procesos hijos
            if ($nivel >= 1) {
                $n = $nivel - 1;
                $campo = 'id_proceso_n' . $n;
                $where = $this->getAdapter()->quoteInto($campo . '= ?', $proceso);
                $flag->update(array('tiene_hijo' => 1), $where);
            }
        }
        echo Zend_Json::encode('Procesos actualizados satisfactoriamente.');
    }

    public function registroActiAction() {

        Zend_Layout::getMvcInstance()->assign('active', 'Registrar actividades y tareas');
        Zend_Layout::getMvcInstance()->assign('padre', 4);
        Zend_Layout::getMvcInstance()->assign('link', 'registroacti');

        $this->view->headScript()->appendFile(SITE_URL . '/js/web/acti-tarea.js');
        $this->view->proceso0 = $this->_proceso0->combo($this->_proyecto);
    }

    public function grabarActividadAction() {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $data = $this->_getAllParams();
        $nivel = $data['n']; //Identifica que nivel de proceso se va a guardar
        $proceso = $data['proceso'];
        if ($nivel == 1) {
            $flag = $this->_proceso1;
        } else if ($nivel == 2) {
            $flag = $this->_proceso2;
        } else if ($nivel == 3) {
            $flag = $this->_proceso3;
        } else if ($nivel == 4) {
            $flag = $this->_proceso4;
        }

        //Considerar un flag para validar si tiene hijos el proceso

        if (!$this->getRequest()->isXmlHttpRequest())
            exit('Acción solo válida para peticiones ajax');

        $actividad = isset($data['actividad']) ? $data['actividad'] : array();
        if (count($actividad) > 0) {

            foreach ($actividad as $reg) {

                $add = explode("|", $reg);
                $actividad = $add[0];
                $maxCodigo = $this->_actividad->obtenerMaxPosicion($nivel, $add[1]) + 1;
                if ($add[0] == 0) { //Nuevo
                    $dataActividad = array('id_actividad' => $add[0], 'descripcion' => $add[2], 'id_proceso' => $add[1], 'nivel' => $nivel,
                        'id_uorganica' => $add[3], 'id_puesto' => $add[4], 'tiene_tarea' => $add[5], 'codigo_actividad' => $maxCodigo,
                        'id_proyecto' => $this->_proyecto, 'usuario_crea' => $this->_usuario, 'fecha_crea' => date("Y-m-d H:i:s"));
                } else {
                    $dataActividad = array('id_actividad' => $add[0], 'descripcion' => $add[2], 'id_proceso' => $add[1], 'nivel' => $nivel,
                        'id_uorganica' => $add[3], 'id_puesto' => $add[4], 'tiene_tarea' => $add[5],
                        'usuario_actu' => $this->_usuario, 'fecha_actu' => date("Y-m-d H:i:s"));
                }

                if ($add[5] == 0) { //No tiene tarea
                    //Cambiar de estado a elimnado a las tareas que tuvo la actividad si antes se registraron tareas
                    $where = $this->getAdapter()->quoteInto('id_actividad = ?', $actividad);
                    $this->_tarea->update(array('estado' => 0), $where);
                }

                $this->_actividad->guardar($dataActividad);
            }

            //Actualizar flag para indicar que tiene procesos hijos
            if ($nivel >= 1) {
                $n = $nivel;
                $campo = 'id_proceso_n' . $n;
                $where = $this->getAdapter()->quoteInto($campo . '= ?', $proceso);
                $flag->update(array('tiene_actividad' => 1), $where);
            }
        }
        echo Zend_Json::encode('Actividades grabadas satisfactoriamente.');
    }

    //Considerar también el nivel que se envíe por el ajax
    public function obtenerActividadAction() {

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

        if ($this->_hasParam('nivel')) {
            $nivel = $this->_getParam('nivel');
            $proceso = $this->_getParam('proceso');
            $dataAct = $this->_actividad->obtenerActividad($this->_proyecto, $proceso, $nivel);

            $contador = 0;
            foreach ($dataAct as $value) {
                $dataAct[$contador]['unidad'] = $this->getHelper('unidadorganica')->select($this->_proyecto, $value['id_uorganica'], $contador + 1);
                $dataAct[$contador]['puesto'] = $this->getHelper('puesto')->select($value['id_uorganica'], $value['id_puesto'], $contador + 1);
                $contador++;
            }

            echo Zend_Json::encode($dataAct);
        }
    }

    public function obtenerProcesoNivel1ActividadAction() {

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

        if ($this->_hasParam('n0')) {
            $n0 = $this->_getParam('n0');
            $nivel = $this->_getParam('nivel');
            $dataProceso1 = $this->_proceso1->obtenerProcesos1Actividad($n0, $nivel);
            echo Zend_Json::encode($dataProceso1);
        }
    }

    public function obtenerProcesoNivel2ActividadAction() {

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

        if ($this->_hasParam('n1')) {
            $n1 = $this->_getParam('n1');
            $nivel = $this->_getParam('nivel');
            $dataProceso2 = $this->_proceso2->obtenerProcesos2Actividad($n1, $nivel);
            echo Zend_Json::encode($dataProceso2);
        }
    }

    public function obtenerProcesoNivel3ActividadAction() {

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

        if ($this->_hasParam('n2')) {
            $n2 = $this->_getParam('n2');
            $nivel = $this->_getParam('nivel');
            $dataProceso3 = $this->_proceso3->obtenerProcesos3Actividad($n2, $nivel);
            echo Zend_Json::encode($dataProceso3);
        }
    }

    public function obtenerProcesoNivel4ActividadAction() {

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

        if ($this->_hasParam('n3')) {
            $n3 = $this->_getParam('n3');
            $nivel = $this->_getParam('nivel');
            $dataProceso4 = $this->_proceso4->obtenerProcesos4Actividad($n3, $nivel);
            echo Zend_Json::encode($dataProceso4);
        }
    }

    public function obtenerUorganicaAction() {

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

        $proyecto = $this->_proyecto;
        $dataUOrganica = $this->_unidad->obtenerUOrganica($proyecto, null);
        $num = $this->_getParam('num');
        $tarea = $this->_getParam('tarea');

        //Enviar select con html
        $option = "<select id='" . $tarea . "unidad_" . $num . "'>";
        $option .= "<option value=''>[Seleccione unidad orgánica]</option>";
        foreach ($dataUOrganica as $value) {
            $option .= "<option value='" . $value['id_uorganica'] . "'>" . $value['descripcion'] . "</option>";
        }
        $option.="</select>";
        echo $option;
    }

    public function obtenerPuestosActividadesAction() {

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

        $unidad = $this->_getParam('unidad');
        $num = $this->_getParam('num');
        $tarea = $this->_getParam('tarea');
        $dataPuesto = $this->_puesto->puestosActividades($unidad);

        //Enviar select con html
        $option = "<select id='" . $tarea . "puesto_" . $num . "'>";
        $option .= "<option value=''>[Seleccione puesto]</option>";
        foreach ($dataPuesto as $value) {
            $option .= "<option value='" . $value['id_puesto'] . "'>" . $value['descripcion'] . "</option>";
        }
        $option.="</select>";
        echo $option;
    }

    //Considerar también el nivel que se envíe por el ajax
    public function obtenerTareaAction() {

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

        if ($this->_hasParam('actividad')) {
            $actividad = $this->_getParam('actividad');
            $dataTarea = $this->_tarea->obtenerTarea($this->_proyecto, $actividad);

            $contador = 0;
            foreach ($dataTarea as $value) {
                $dataTarea[$contador]['unidad'] = $this->getHelper('unidadorganica')->select($this->_proyecto, $value['id_uorganica'], $contador + 1, 't');
                $dataTarea[$contador]['puesto'] = $this->getHelper('puesto')->select($value['id_uorganica'], $value['id_puesto'], $contador + 1, 't');
                $contador++;
            }
            echo Zend_Json::encode($dataTarea);
        }
    }

    public function grabarTareaAction() {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $data = $this->_getAllParams();
        $actividad = $data['actividad'];

        //Considerar un flag para validar si tiene hijos el proceso

        if (!$this->getRequest()->isXmlHttpRequest())
            exit('Acción solo válida para peticiones ajax');

        $tarea = isset($data['tarea']) ? $data['tarea'] : array();
        if (count($tarea) > 0) {

            foreach ($tarea as $reg) {

                $add = explode("|", $reg);
                
                //$actividad = $add[0];
                $maxCodigo = $this->_tarea->obtenerMaxPosicion($add[1]) + 1;
                if ($add[0] == 0) { //Nuevo
                    $dataTarea = array('id_tarea' => $add[0], 'descripcion' => $add[2], 'id_actividad' => $add[1],
                        'id_uorganica' => $add[3], 'id_puesto' => $add[4], 'codigo_tarea' => $maxCodigo,
                        'id_proyecto' => $this->_proyecto, 'usuario_crea' => $this->_usuario, 'fecha_crea' => date("Y-m-d H:i:s"));
                } else {
                    $dataTarea = array('id_tarea' => $add[0], 'descripcion' => $add[2], 'id_actividad' => $add[1],
                        'id_uorganica' => $add[3], 'id_puesto' => $add[4],
                        'usuario_actu' => $this->_usuario, 'fecha_actu' => date("Y-m-d H:i:s"));
                }
                $this->_tarea->guardar($dataTarea);
            }

            //Actualizar flag para indicar que tiene tareas la actividad
            //Y actualizar campos de unidad orgánica y puesto 
            $campo = 'id_actividad';
            $where = $this->getAdapter()->quoteInto($campo . '= ?', $actividad);
            $this->_actividad->update(array('tiene_tarea' => 1, 'id_uorganica' => '', 'id_puesto' => ''), $where);
        }
        echo Zend_Json::encode('Tareas grabadas satisfactoriamente.');
    }

    public function cambiarPosicionAction() {

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

        if ($data['tipo'] == 'actividad') {
            $dataRpta = $this->_actividad->cambiarPosicion($data['nivel'], $data['proceso'], $data['actividad'], $data['anterior'], $data['nueva']);
        } else if ($data['tipo'] == 'tarea') {
            $dataRpta = $this->_tarea->cambiarPosicion($data['actividad'], $data['tarea'], $data['anterior'], $data['nueva']);
        }

        echo Zend_Json::encode($dataRpta);
    }

}
