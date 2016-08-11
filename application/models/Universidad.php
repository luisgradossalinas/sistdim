<?php

class Application_Model_Universidad extends Zend_Db_Table
{

    protected $_name = 'universidad';

    protected $_primary = 'id_universidad';

    const ESTADO_INACTIVO = 0;

    const ESTADO_ACTIVO = 1;

    const ESTADO_ELIMINADO = 2;

    const TABLA = 'universidad';

    public function guardar($datos)
    {
        $id = 0;
        if (!empty($datos["id"])) {
        	$id = (int) $datos["id"];
        }
        
        unset($datos["id"]);
        $datos = array_intersect_key($datos, array_flip($this->_getCols()));
        
        if ($id > 0) {
        	$cantidad = $this->update($datos, 'id_universidad = ' . $id);
        	$id = ($cantidad < 1) ? 0 : $id;
        } else {
        	$GM = new Generator_Modelo();
        	$datos['id_universidad'] = $GM->maxCodigo($this->_name);
        	$id = $this->insert($datos);
        }
        
        return $id;
    }

    public function listado()
    {
        return $this->getAdapter()->select()->from($this->_name)->query()->fetchAll();
    }


}

