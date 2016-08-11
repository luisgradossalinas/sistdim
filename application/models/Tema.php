<?php

class Application_Model_Tema extends Zend_Db_Table
{

    protected $_name = 'tema';

    protected $_primary = 'id_tema';

    const ESTADO_INACTIVO = 0;

    const ESTADO_ACTIVO = 1;

    const ESTADO_ELIMINADO = 2;

    const TABLA = 'tema';

    public function guardar($datos)
    {
        $id = 0;
        if (!empty($datos["id"])) {
        	$id = (int) $datos["id"];
        }
        
        unset($datos["id"]);
        $datos = array_intersect_key($datos, array_flip($this->_getCols()));
        
        if ($id > 0) {
        	$cantidad = $this->update($datos, 'id_tema = ' . $id);
        	$id = ($cantidad < 1) ? 0 : $id;
        } else {
                $datos['fecha_inicio'] = new Zend_Date($datos['fecha_inicio'],'dd/mm/yyyy');
                $datos['fecha_inicio'] = $datos['fecha_inicio']->get('yyyy-mm-dd');
        	$id = $this->insert($datos);
        }
        
        return $id;
    }

    public function listado()
    {
        return $this->getAdapter()->select()->from($this->_name)->query()->fetchAll();
    }


}

