<?php

class Application_Model_Periocidad extends Zend_Db_Table
{

    protected $_name = 'periocidad';
    protected $_primary = 'id_periodicidad';

    const ESTADO_INACTIVO = 0;
    const ESTADO_ACTIVO = 1;
    const ESTADO_ELIMINADO = 2;
    const TABLA = 'periocidad';

    public function guardar($datos)
    {
        $id = 0;
        if (!empty($datos["id_periodicidad"])) {
        	$id = (int) $datos["id_periodicidad"];
        }
        
        unset($datos["id"]);
        $datos = array_intersect_key($datos, array_flip($this->_getCols()));
        
        if ($id > 0) {
            if (isset($datos['fecha_crea']) && !empty($datos['fecha_crea'])) {
        	$datos['fecha_crea'] = new Zend_Date($datos['fecha_crea'],'yyyy-mm-dd');
        	$datos['fecha_crea'] = $datos['fecha_crea']->get('yyyy-mm-dd');
            }
            if (isset($datos['fecha_actu']) && !empty($datos['fecha_actu'])) {
        	$datos['fecha_actu'] = new Zend_Date($datos['fecha_actu'],'yyyy-mm-dd');
        	$datos['fecha_actu'] = $datos['fecha_actu']->get('yyyy-mm-dd');
            }
        	$cantidad = $this->update($datos, 'id_periodicidad = ' . $id);
        	$id = ($cantidad < 1) ? 0 : $id;
        } else {
            if (isset($datos['fecha_crea']) && !empty($datos['fecha_crea'])) {
        	$datos['fecha_crea'] = new Zend_Date($datos['fecha_crea'],'yyyy-mm-dd');
        	$datos['fecha_crea'] = $datos['fecha_crea']->get('yyyy-mm-dd');
            }
            if (isset($datos['fecha_actu']) && !empty($datos['fecha_actu'])) {
        	$datos['fecha_actu'] = new Zend_Date($datos['fecha_actu'],'yyyy-mm-dd');
        	$datos['fecha_actu'] = $datos['fecha_actu']->get('yyyy-mm-dd');
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

