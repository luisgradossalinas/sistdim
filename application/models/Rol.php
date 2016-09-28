<?php

class Application_Model_Rol extends Zend_Db_Table
{
    protected $_name = 'rol';
    protected $_primary = 'id';
    
    const ADMINISTRADOR = 1;
    const CLIENTE_WEB = 2;
    const SUPER = 3;
    const NOMADMIN = "Administrador";
    
    const INVITADO = 'Invitado';
    
    const ESTADO_INACTIVO = 0;
    const ESTADO_ACTIVO = 1;
    const ESTADO_ELIMINADO = 2;
    
    const TABLA = 'rol';
    
    public function combo()
    {
        return $this->getAdapter()->select()->from($this->_name,array('key' => 'id', 'value' => 'nombre'))
                ->where('estado = ?',self::ESTADO_ACTIVO)->query()->fetchAll();
    }
    
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

