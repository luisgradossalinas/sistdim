<?php

class Application_Model_Tarea extends Zend_Db_Table
{
    protected $_name = 'tarea';

    protected $_primary = 'id_tarea';

    const ESTADO_INACTIVO = 0;
    const ESTADO_ACTIVO = 1;
    const ESTADO_ELIMINADO = 2;
    const TABLA = 'tarea';

    public function guardar($datos)
    {
        $id = 0;
        if (!empty($datos["id_tarea"])) {
        	$id = (int) $datos["id_tarea"];
        }
        
        unset($datos["id"]);
        $datos = array_intersect_key($datos, array_flip($this->_getCols()));
        
        if ($id > 0) {
        	$cantidad = $this->update($datos, 'id_tarea = ' . $id);
        	$id = ($cantidad < 1) ? 0 : $id;
        } else {
        	$id = $this->insert($datos);
        }
        return $id;
    }

    public function combo()
    {
        return $this->getAdapter()->select()->from($this->_name,array('key' => 'id_tarea', 'value' => 'descripcion'))
                ->where('estado = ?',self::ESTADO_ACTIVO)->query()->fetchAll();
    }
    
    public function obtenerTarea($proyecto, $actividad)
    {
        return $this->getAdapter()->select()->from($this->_name)
                ->where('id_proyecto = ?', $proyecto)
                ->where('id_actividad = ?', $actividad)
                ->where('estado = ?', self::ESTADO_ACTIVO)
                ->query()->fetchAll();
    }
}

