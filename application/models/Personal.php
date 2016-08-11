<?php

class Application_Model_Personal extends Zend_Db_Table
{

    protected $_name = 'personal';

    protected $_primary = 'id_personal';

    const ESTADO_INACTIVO = 0;

    const ESTADO_ACTIVO = 1;

    const ESTADO_ELIMINADO = 2;

    const TABLA = 'personal';

    public function guardar($datos)
    {
        $id = 0;
        if (!empty($datos["id"])) {
        	$id = (int) $datos["id"];
        }
        
        unset($datos["id"]);
        $datos = array_intersect_key($datos, array_flip($this->_getCols()));
        
        if ($id > 0) {
        	$cantidad = $this->update($datos, 'id_personal = ' . $id);
        	$id = ($cantidad < 1) ? 0 : $id;
        } else {
        	$GM = new Generator_Modelo();
        	$datos['id_personal'] = $GM->maxCodigo($this->_name);
        	$id = $this->insert($datos);
        }
        
        return $id;
    }

    public function listado()
    {
        return $this->getAdapter()->select()->from($this->_name)->query()->fetchAll();
    }


}

