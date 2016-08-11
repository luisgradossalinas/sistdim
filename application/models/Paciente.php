<?php

class Application_Model_Paciente extends Zend_Db_Table
{

    protected $_name = 'paciente';

    protected $_primary = 'id';

    const ESTADO_INACTIVO = 0;

    const ESTADO_ACTIVO = 1;

    const ESTADO_ELIMINADO = 2;

    const TABLA = 'paciente';

    public function guardar($datos)
    {
        $id = 0;
        if (!empty($datos["id"])) {
        	$id = (int) $datos["id"];
        }
        
        unset($datos["id"]);
        $datos = array_intersect_key($datos, array_flip($this->_getCols()));
        
        if ($id > 0) {
            if (isset($datos['fecha_nacimiento']) && !empty($datos['fecha_nacimiento'])) {
        	$datos['fecha_nacimiento'] = new Zend_Date($datos['fecha_nacimiento'],'yyyy-mm-dd');
        	$datos['fecha_nacimiento'] = $datos['fecha_nacimiento']->get('yyyy-mm-dd');
            }
            if (isset($datos['fh_registro']) && !empty($datos['fh_registro'])) {
        	$datos['fh_registro'] = new Zend_Date($datos['fh_registro'],'yyyy-mm-dd');
        	$datos['fh_registro'] = $datos['fh_registro']->get('yyyy-mm-dd');
            }
        	$cantidad = $this->update($datos, 'id = ' . $id);
        	$id = ($cantidad < 1) ? 0 : $id;
        } else {
            if (isset($datos['fecha_nacimiento']) && !empty($datos['fecha_nacimiento'])) {
        	$datos['fecha_nacimiento'] = new Zend_Date($datos['fecha_nacimiento'],'yyyy-mm-dd');
        	$datos['fecha_nacimiento'] = $datos['fecha_nacimiento']->get('yyyy-mm-dd');
            }
            if (isset($datos['fh_registro']) && !empty($datos['fh_registro'])) {
        	$datos['fh_registro'] = new Zend_Date($datos['fh_registro'],'yyyy-mm-dd');
        	$datos['fh_registro'] = $datos['fh_registro']->get('yyyy-mm-dd');
            }
        	$id = $this->insert($datos);
        }
        
        return $id;
    }

    public function listado()
    {
        return $this->getAdapter()->select()->from($this->_name)->query()->fetchAll();
    }


}

