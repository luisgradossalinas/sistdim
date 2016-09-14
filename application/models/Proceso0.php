<?php

class Application_Model_Proceso0 extends Zend_Db_Table
{

    protected $_name = 'proceso_n0';
    protected $_primary = 'id_proceso_n0';

    const ESTADO_INACTIVO = 0;
    const ESTADO_ACTIVO = 1;
    const ESTADO_ELIMINADO = 2;
    const TABLA = 'proceso_n0';
    const TIENE_HIJO = 1;

    public function guardar($datos)
    {
        $id = 0;
        if (!empty($datos["id_proceso_n0"])) {
        	$id = (int) $datos["id_proceso_n0"];
        }
        
        unset($datos["id"]);
        $datos = array_intersect_key($datos, array_flip($this->_getCols()));
        
        if ($id > 0) {
        	$cantidad = $this->update($datos, $this->_primary .' = ' . $id);
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
    
    public function combo($proyecto)
    {
        return $this->getAdapter()->select()->from($this->_name,array('key' => $this->_primary, 'value' => 'descripcion'))
                ->where('estado = ?',self::ESTADO_ACTIVO)
                ->where('id_proyecto = ?', $proyecto)
                ->order('descripcion asc')
                ->query()->fetchAll();
    }
    
    public function obtenerProcesos0($proyecto)
    {
        return $this->getAdapter()->select()->from($this->_name)
                ->where('estado = ?',self::ESTADO_ACTIVO)
                ->where('id_proyecto = ?', $proyecto)
                ->order('descripcion asc')
                //->order(array('codigo_tipoproceso desc','descripcion asc'))
                ->query()->fetchAll();
    }


}

