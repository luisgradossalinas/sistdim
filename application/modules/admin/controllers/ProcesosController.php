<?php

class Admin_ProcesosController extends App_Controller_Action_Admin {

    private $_proceso0;
    private $_proceso1;
    private $_proceso2;
    private $_proceso3;
    private $_proceso4;
    private $_usuario;
    private $_proyecto;
    private $_tipoProceso;

    public function init() {

        $this->_proceso0 = new Application_Model_Proceso0;
        $this->_proceso1 = new Application_Model_Proceso1;
        $this->_proceso2 = new Application_Model_Proceso2;
        $this->_proceso3 = new Application_Model_Proceso3;
        $this->_proceso4 = new Application_Model_Proceso4;
        $this->_tipoProceso = new Application_Model_Tipoproceso;

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
        } else if ($nivel == 2) {
            $modelo = $this->_proceso2;
        } else if ($nivel == 3) {
            $modelo = $this->_proceso3;
        } else if ($nivel == 4) {
            $modelo = $this->_proceso4;
        }

        if (!$this->getRequest()->isXmlHttpRequest())
            exit('Acción solo válida para peticiones ajax');

        $procesos = isset($data['procesos']) ? $data['procesos'] : array();
        if (count($procesos) > 0) {
            foreach ($procesos as $reg) {

                $add = explode("|", $reg);
                if ($add[0] == 0) { //Nuevo
                    if ($nivel == 0) {
                        $dataProceso = array('id_proceso_n0' => $add[0], 'descripcion' => $add[2], 'codigo_tipoproceso' => $add[1],
                            'id_proyecto' => $this->_proyecto,'usuario_crea' => $this->_usuario, 'fecha_crea' => date("Y-m-d H:i:s"));
                    } else if ($nivel == 1) {
                        $dataProceso = array('id_proceso_n1' => $add[0],'id_proceso_n0' => $add[1], 'descripcion' => $add[2],
                            'id_proyecto' => $this->_proyecto,'usuario_crea' => $this->_usuario, 'fecha_crea' => date("Y-m-d H:i:s"));
                    } else if ($nivel == 2) {
                        $dataProceso = array('id_proceso_n2' => $add[0],'id_proceso_n1' => $add[1], 'descripcion' => $add[2],
                            'id_proyecto' => $this->_proyecto,'usuario_crea' => $this->_usuario, 'fecha_crea' => date("Y-m-d H:i:s"));
                    } else if ($nivel == 3) {
                        $dataProceso = array('id_proceso_n3' => $add[0],'id_proceso_n2' => $add[1], 'descripcion' => $add[2],
                            'id_proyecto' => $this->_proyecto,'usuario_crea' => $this->_usuario, 'fecha_crea' => date("Y-m-d H:i:s"));
                    } else if ($nivel == 4) {
                        $dataProceso = array('id_proceso_n4' => $add[0],'id_proceso_n3' => $add[1], 'descripcion' => $add[2],
                            'id_proyecto' => $this->_proyecto,'usuario_crea' => $this->_usuario, 'fecha_crea' => date("Y-m-d H:i:s"));
                    }
                } else {
                    if ($nivel == 0) {
                        $dataProceso = array('id_proceso_n0' => $add[0], 'descripcion' => $add[2], 'codigo_tipoproceso' => $add[1],
                            'usuario_actu' => $this->_usuario, 'fecha_actu' => date("Y-m-d H:i:s"));
                    } else if ($nivel == 1) {
                        $dataProceso = array('id_proceso_n1' => $add[0],'id_proceso_n0' => $add[1], 'descripcion' => $add[2], 
                            'usuario_actu' => $this->_usuario, 'fecha_actu' => date("Y-m-d H:i:s"));
                    } else if ($nivel == 2) {
                        $dataProceso = array('id_proceso_n2' => $add[0],'id_proceso_n1' => $add[1], 'descripcion' => $add[2], 
                            'usuario_actu' => $this->_usuario, 'fecha_actu' => date("Y-m-d H:i:s"));
                    } else if ($nivel == 3) {
                        $dataProceso = array('id_proceso_n3' => $add[0],'id_proceso_n2' => $add[1], 'descripcion' => $add[2], 
                            'usuario_actu' => $this->_usuario, 'fecha_actu' => date("Y-m-d H:i:s"));
                    } else if ($nivel == 4) {
                        $dataProceso = array('id_proceso_n4' => $add[0],'id_proceso_n3' => $add[1], 'descripcion' => $add[2], 
                            'usuario_actu' => $this->_usuario, 'fecha_actu' => date("Y-m-d H:i:s"));
                    }
                }
                $modelo->guardar($dataProceso);
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
        $modelo = '';
        if ($nivel == 0) {
            $modelo = $this->_proceso0;
        } else if ($nivel == 1) {
            $modelo = $this->_proceso1;
        } else if ($nivel == 2) {
            $modelo = $this->_proceso2;
        } else if ($nivel == 3) {
            $modelo = $this->_proceso3;
        } else if ($nivel == 4) {
            $modelo = $this->_proceso4;
        }

        if (!$this->getRequest()->isXmlHttpRequest())
            exit('Acción solo válida para peticiones ajax');

        $procesos = isset($data['procesos']) ? $data['procesos'] : array();
        if (count($procesos) > 0) {
            foreach ($procesos as $reg) {

                $add = explode("|", $reg);
                if ($add[0] == 0) { //Nuevo
                    if ($nivel == 0) {
                        $dataProceso = array('id_proceso_n0' => $add[0], 'descripcion' => $add[2], 'codigo_tipoproceso' => $add[1],
                            'id_proyecto' => $this->_proyecto,'usuario_crea' => $this->_usuario, 'fecha_crea' => date("Y-m-d H:i:s"));
                    } else if ($nivel == 1) {
                        $dataProceso = array('id_proceso_n1' => $add[0],'id_proceso_n0' => $add[1], 'descripcion' => $add[2],
                            'id_proyecto' => $this->_proyecto,'usuario_crea' => $this->_usuario, 'fecha_crea' => date("Y-m-d H:i:s"));
                    } else if ($nivel == 2) {
                        $dataProceso = array('id_proceso_n2' => $add[0],'id_proceso_n1' => $add[1], 'descripcion' => $add[2],
                            'id_proyecto' => $this->_proyecto,'usuario_crea' => $this->_usuario, 'fecha_crea' => date("Y-m-d H:i:s"));
                    } else if ($nivel == 3) {
                        $dataProceso = array('id_proceso_n3' => $add[0],'id_proceso_n2' => $add[1], 'descripcion' => $add[2],
                            'id_proyecto' => $this->_proyecto,'usuario_crea' => $this->_usuario, 'fecha_crea' => date("Y-m-d H:i:s"));
                    } else if ($nivel == 4) {
                        $dataProceso = array('id_proceso_n4' => $add[0],'id_proceso_n3' => $add[1], 'descripcion' => $add[2],
                            'id_proyecto' => $this->_proyecto,'usuario_crea' => $this->_usuario, 'fecha_crea' => date("Y-m-d H:i:s"));
                    }
                } else {
                    if ($nivel == 0) {
                        $dataProceso = array('id_proceso_n0' => $add[0], 'descripcion' => $add[2], 'codigo_tipoproceso' => $add[1],
                            'usuario_actu' => $this->_usuario, 'fecha_actu' => date("Y-m-d H:i:s"));
                    } else if ($nivel == 1) {
                        $dataProceso = array('id_proceso_n1' => $add[0],'id_proceso_n0' => $add[1], 'descripcion' => $add[2], 
                            'usuario_actu' => $this->_usuario, 'fecha_actu' => date("Y-m-d H:i:s"));
                    } else if ($nivel == 2) {
                        $dataProceso = array('id_proceso_n2' => $add[0],'id_proceso_n1' => $add[1], 'descripcion' => $add[2], 
                            'usuario_actu' => $this->_usuario, 'fecha_actu' => date("Y-m-d H:i:s"));
                    } else if ($nivel == 3) {
                        $dataProceso = array('id_proceso_n3' => $add[0],'id_proceso_n2' => $add[1], 'descripcion' => $add[2], 
                            'usuario_actu' => $this->_usuario, 'fecha_actu' => date("Y-m-d H:i:s"));
                    } else if ($nivel == 4) {
                        $dataProceso = array('id_proceso_n4' => $add[0],'id_proceso_n3' => $add[1], 'descripcion' => $add[2], 
                            'usuario_actu' => $this->_usuario, 'fecha_actu' => date("Y-m-d H:i:s"));
                    }
                }
                $modelo->guardar($dataProceso);
            }
        }
        echo Zend_Json::encode('Procesos actualizados satisfactoriamente.');
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
            $dataAct = $this->_proceso4->obtenerProcesos4($nivel, $proceso);
            echo Zend_Json::encode($dataAct);
        }
    }

}
