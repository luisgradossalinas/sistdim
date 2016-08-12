<?php

class Application_Model_Grupo extends Zend_Db_Table
{

    protected $_name = 'grupo';

    protected $_primary = 'codigo_grupo';

    const ESTADO_INACTIVO = 0;

    const ESTADO_ACTIVO = 1;

    const ESTADO_ELIMINADO = 2;

    const TABLA = 'grupo';

    public function guardar($datos)
    {
        $id = 0;
        if (!empty($datos["id"])) {
        	$id = (int) $datos["id"];
        }
        
        unset($datos["id"]);
        $datos = array_intersect_key($datos, array_flip($this->_getCols()));
        
        if ($id > 0) {
        	$cantidad = $this->update($datos, 'codigo_grupo = ' . $id);
        	$id = ($cantidad < 1) ? 0 : $id;
        } else {
        	$id = $this->insert($datos);
        }
        
        return $id;
    }

    public function listado()
    {
        return $this->getAdapter()->select()->from($this->_name)->query()->fetchAll();
    }
    
    public function combo()
    {
        return $this->getAdapter()->select()->from($this->_name,array('key' => 'codigo_grupo', 'value' => 'descripcion'))
                ->where('estado = ?',self::ESTADO_ACTIVO)->query()->fetchAll();
    }


}

