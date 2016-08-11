<?php

class Application_Model_Configuracion extends Zend_Db_Table
{
    protected $_name = 'configuracion';
    protected $_primary = 'id';
    
    const ESTADO_INACTIVO = 0;
    const ESTADO_ACTIVO = 1;
    const ESTADO_ELIMINADO = 2;
    
    const TABLA = 'configuracion';
    
    const ESTILO_PANEL = 'ESTILO_PANEL';
    static $_ARRAY_ESTILO_PANEL = array('blue', 'red', 'grey');
    
    public function guardar($datos)
    {         
        $id = 0;
        if (!empty($datos['id'])) {
            $id = (int) $datos['id'];
        }
        unset($datos['id']);

        $datos = array_intersect_key($datos, array_flip($this->_getCols()));

        if ($id > 0) {
            $cantidad = $this->update($datos, 'id = ' . $id);
            $id = ($cantidad < 1) ? 0 : $id;
        } else {
            $id = $this->insert($datos);
        }
        return $id;
    }


}

