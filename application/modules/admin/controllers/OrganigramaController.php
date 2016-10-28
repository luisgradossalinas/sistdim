<?php

class Admin_OrganigramaController extends App_Controller_Action_Admin {

    private $_organoModel;
    private $_organoForm;
    private $_unidadModel;
    private $_unidadForm;
    private $_puestoModel;
    private $_familiaModel;
    private $_rolPuestoModel;
    private $_grupoModel;
    private $_nivelPuesto;
    private $_categoriaPuesto;
    private $_proyecto;
    private $_usuario;
    private $_mapaPuesto;

    const INACTIVO = 0;
    const ACTIVO = 1;
    const ELIMINADO = 2;

    public function init() {

        $this->_organoModel = new Application_Model_Organo;
        $this->_organoForm = new Application_Form_Organo;
        $this->_unidadModel = new Application_Model_UnidadOrganica;
        $this->_unidadForm = new Application_Form_UnidadOrganica;
        $this->_puestoModel = new Application_Model_Puesto;
        $this->_grupoModel = new Application_Model_Grupo;
        $this->_familiaModel = new Application_Model_Familia;
        $this->_rolPuestoModel = new Application_Model_Rolpuesto;
        $this->_nivelPuesto = new Application_Model_Nivelpuesto;
        $this->_categoriaPuesto = new Application_Model_Categoriapuesto;

        $sesion_usuario = new Zend_Session_Namespace('sesion_usuario');
        $this->_proyecto = $sesion_usuario->sesion_usuario['id_proyecto'];
        $this->_usuario = $sesion_usuario->sesion_usuario['id'];
        $this->_mapaPuesto = $sesion_usuario->sesion_usuario['mapa_puesto'];
        
        $this->view->headScript()->appendFile(SITE_URL . '/js/web/organigrama.js');
        Zend_Layout::getMvcInstance()->assign('show', '1'); //No mostrar en el menú la barra horizontal
        parent::init();
    }

    public function indexAction() {

        Zend_Layout::getMvcInstance()->assign('active', 'Organigrama');
        Zend_Layout::getMvcInstance()->assign('padre', 3);
        Zend_Layout::getMvcInstance()->assign('link', 'organigrama');

        $this->view->organo = $this->_organoModel->obtenerOrgano($this->_proyecto);
        $this->view->unidad = $this->_unidadModel->obtenerUOrganica($this->_proyecto, null);
    }

    public function operacionAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $sesionMvc = new Zend_Session_Namespace('sesion_mvc');
        $data = $this->_getAllParams();

        //Previene vulnerabilidad XSS (Cross-site scripting)
        $filtro = new Zend_Filter_StripTags();
        foreach ($data as $key => $val) {
            $data[$key] = $filtro->filter(trim($val));
        }

        $tipo = $data['tipo'];    
        if ($tipo == 'organo') {
            $modelo = $this->_organoModel;
            $form = $this->_organoForm;
            $primary = "id_organo";
        } else if ($tipo == 'unidad') {
            $modelo = $this->_unidadModel;
            $form = $this->_unidadForm;
            $primary = "id_uorganica";
        }

        if ($this->_getParam('ajax') == 'form') {
            if ($this->_hasParam('id')) {
                $id = $this->_getParam('id');
                if ($id != 0) {
                    $data = $modelo->fetchRow($primary . '= ' . $id);
                    $form->populate($data->toArray());
                }
            }
            //$this->_organoForm->getElement('email')->setAttrib('readonly','readonly');   
            //$this->_organoForm->removeElement('unidad_organica');
            echo $form;
        }

        if ($this->_getParam('ajax') == 'validar') {
            echo $form->processAjax($data);
        }
        if ($this->_getParam('ajax') == 'delete') {
            
            //Validar primero en algunos casos, si tiene órganos activos
            if ($tipo == Application_Model_Organo::TABLA) {
                $unidadOrganica = new Application_Model_UnidadOrganica();
                $dataValida = $unidadOrganica->obtenerUOrganica($this->_proyecto, $data['id']); //Verifica si existen uorganicas activas
                if (count($dataValida) > 0) {
                    echo Zend_Json::encode(array('code' => 0, 'msg' => 'No se puede eliminar el órgano,'
                        . ' tiene unidades orgánicas activas.'));
                    return;
                }
            }
            
            //Validar primero en algunos casos, si tiene puestos activos
            if ($tipo == 'unidad') {
                $puesto = new Application_Model_Puesto;
                $dataValida = $puesto->obtenerPuestos($data['id']); //Verifica si existen uorganicas activas
                if (count($dataValida) > 0) {
                    echo Zend_Json::encode(array('code' => 0, 'msg' => 'No se puede eliminar la unidad orgánica,'
                        . ' tiene puestos activos.'));
                    return;
                }
            }

            $where = $this->getAdapter()->quoteInto($primary . ' = ?', $data['id']);
            $modelo->update(array('estado' => self::ELIMINADO), $where);
            $sesionMvc->messages = 'Registro eliminado';
            $sesionMvc->tipoMessages = self::SUCCESS;
            
            echo Zend_Json::encode(array('code' => 1, 'msg' => 'Registro eliminado'));
            
        }

        if ($this->_getParam('ajax') == 'save') {

            if ($this->_getParam('scrud') == 'nuevo') {
                $data['fecha_crea'] = date("Y-m-d H:i:s");
                $data['usuario_crea'] = Zend_Auth::getInstance()->getIdentity()->id;
                $data['id_proyecto'] = $this->_proyecto;
                $sesionMvc->messages = 'Registro grabado satisfactoriamente';
            } else {
                $data['fecha_actu'] = date("Y-m-d H:i:s");
                $data['usuario_actu'] = Zend_Auth::getInstance()->getIdentity()->id;
                $data['id_proyecto'] = $this->_proyecto;
                $sesionMvc->messages = 'Registro actualizado satisfactoriamente';
            }

            $sesionMvc->tipoMessages = self::SUCCESS;
            $modelo->guardar($data);
            echo Zend_Json::encode($sesionMvc->messages);
        }
    }

    /*
      Actualizar registros de órganos y unidades orgánica
    */
    public function grabarAction() {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if (!$this->getRequest()->isXmlHttpRequest())
            exit('Acción solo válida para peticiones ajax');

        $data = $this->_getAllParams();
        $datos = $data['datos'];
        $tipo = $data['tipo'];

        if ($tipo == 'organo') {
            $modelo = $this->_organoModel;
        } else if ($tipo == 'unidad') {
            $modelo = $this->_unidadModel;
        }

        if (count($datos) > 0) {
            foreach ($datos as $reg) {
                $add = explode("|", $reg);
                if ($tipo == 'organo') {
                    $where = $this->getAdapter()->quoteInto('id_organo = ?', $add[0]);
                    $dataNueva = array('organo' => $add[1], 'codigo_natuorganica' => $add[2],'siglas' => $add[3],
                        'usuario_actu' => $this->_usuario, 'fecha_actu' => date("Y-m-d H:i:s"));
                } else {
                    $where = $this->getAdapter()->quoteInto('id_uorganica = ?', $add[0]);
                    $dataNueva = array('descripcion' => $add[1], 'id_organo' => $add[2],'siglas' => $add[3],
                        'usuario_actu' => $this->_usuario, 'fecha_actu' => date("Y-m-d H:i:s"));
                }
                $modelo->update($dataNueva, $where);
            }
        }
        echo Zend_Json::encode('Registros actualizados');
    }

    public function puestoAction() {

        Zend_Layout::getMvcInstance()->assign('active', 'Registrar puestos');
        Zend_Layout::getMvcInstance()->assign('padre', 3);
        Zend_Layout::getMvcInstance()->assign('link', 'regpuestos');
        //Listado de órganos registrados del proyecto
        $this->view->organo = $this->_organoModel->obtenerOrgano($this->_proyecto);
        //$this->view->unidad = $this->_unidadModel->obtenerUOrganica($this->_proyecto, null);
        $this->view->mapaPuesto = $this->_mapaPuesto;
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

        if ($this->_hasParam('organo')) {
            $organo = $this->_getParam('organo');
            $proyecto = $this->_proyecto;
            $dataUOrganica = $this->_unidadModel->obtenerUOrganica($proyecto, $organo);

            //Enviar select con html
            $option = "<select id='unidad' style='width:320px'>";
            $option .= "<option value=''>[Seleccione unidad orgánica]</option>";
            foreach ($dataUOrganica as $value) {
                $option .= "<option value='" . $value['id_uorganica'] . "'>" . $value['descripcion'] . "</option>";
            }
            $option.="</select>";
            echo $option;
            //echo Zend_Json::encode($dataUOrganica);
        }
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
        $dataPuesto = $this->_puestoModel->obtenerPuestos($unidad);
        $contador = 0;
        /*
        foreach ($dataPuesto as $value) {
            $dataPuesto[$contador]['ngrupo'] = $dataPuesto[$contador]['grupo'];
            $dataPuesto[$contador]['nfamilia'] = $dataPuesto[$contador]['familia'];
            $dataPuesto[$contador]['npuesto'] = $dataPuesto[$contador]['rpuesto'];
            
            $dataPuesto[$contador]['grupo'] = $this->getHelper('grupo')->select($value['codigo_grupo'], $contador + 1);
            $dataPuesto[$contador]['familia'] = $this->getHelper('familia')->select($value['codigo_grupo'], $value['codigo_familia'], $contador + 1);
            $dataPuesto[$contador]['rpuesto'] = $this->getHelper('rolpuesto')->select($value['codigo_familia'], $value['codigo_rol_puesto'], $contador + 1);
            $contador++;
        }
        */
        echo Zend_Json::encode($dataPuesto);
    }

    public function obtenerGruposAction() {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        if (!$this->getRequest()->isXmlHttpRequest())
            exit('Acción solo válida para peticiones ajax');

        $dataGrupo = $this->_grupoModel->listado();
        echo Zend_Json::encode($dataGrupo);
    }

    public function obtenerFamiliasAction() {

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

        $dataFamilia = $this->_familiaModel->obtenerFamilias($data['grupo']);
        echo Zend_Json::encode($dataFamilia);
    }

    public function obtenerRolesAction() {

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

        $dataRol = $this->_rolPuestoModel->obtenerRoles($data['familia']);
        echo Zend_Json::encode($dataRol);
    }

    public function grabarPuestosAction() {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $data = $this->_getAllParams();
        if (!$this->getRequest()->isXmlHttpRequest())
            exit('Acción solo válida para peticiones ajax');

        $puestos = isset($data['puestos']) ? $data['puestos'] : array();
        if (count($puestos) > 0) {
            foreach ($puestos as $reg) {
                $add = explode("|", $reg);
                if ($add[0] == 0) { //Nuevo
                    $dataNueva = array('id_puesto' => $add[0], 'descripcion' => $add[2], 'id_uorganica' => $add[4],
                    'num_correlativo' => $add[1], 'cantidad' => $add[3], 
                    //'codigo_grupo' => $add[4],'codigo_familia' => $add[5], 'codigo_rol_puesto' => $add[6],
                    'nombre_trabajador' => '','nombre_personal' => $add[5],
                    'usuario_crea' => $this->_usuario, 'fecha_crea' => date("Y-m-d H:i:s"));
                } else { //existe
                    $dataNueva = array('id_puesto' => $add[0], 'descripcion' => $add[2], 'id_uorganica' => $add[4],
                    'num_correlativo' => $add[1], 'cantidad' => $add[3], 
                    // 'codigo_grupo' => $add[4], 'codigo_familia' => $add[5], 'codigo_rol_puesto' => $add[6],
                     'nombre_personal' => $add[5],'usuario_actu' => $this->_usuario, 'fecha_actu' => date("Y-m-d H:i:s"));
                
                }$this->_puestoModel->guardar($dataNueva);
            }
        }

        echo Zend_Json::encode('Puestos actualizados satisfactoriamente.');
    }
    
    public function obtenerNivelPuestoAction() {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        if (!$this->getRequest()->isXmlHttpRequest())
            exit('Acción solo válida para peticiones ajax');
        
        $data = $this->_getAllParams();
        //Previene vulnerabilidad XSS (Cross-site scripting)
        $filtro = new Zend_Filter_StripTags();
        foreach ($data as $key => $val) {
            $data[$key] = $filtro->filter(trim($val));
        }
        
        $dataNivel = $this->_nivelPuesto->obtenerNiveles($data['grupo']);
        echo Zend_Json::encode($dataNivel);
    }
    
    public function obtenerCategoriaPuestoAction() {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        if (!$this->getRequest()->isXmlHttpRequest())
            exit('Acción solo válida para peticiones ajax');
        
        $data = $this->_getAllParams();
        //Previene vulnerabilidad XSS (Cross-site scripting)
        $filtro = new Zend_Filter_StripTags();
        foreach ($data as $key => $val) {
            $data[$key] = $filtro->filter(trim($val));
        }
        
        $dataCategoria = $this->_categoriaPuesto->obtenerCategoria($data['familia']);
        echo Zend_Json::encode($dataCategoria);
    }

}
