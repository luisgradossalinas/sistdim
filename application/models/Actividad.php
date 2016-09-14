<?php

class Application_Model_Actividad extends Zend_Db_Table
{

    protected $_name = 'actividad';

    protected $_primary = 'id_actividad';

    const ESTADO_INACTIVO = 0;

    const ESTADO_ACTIVO = 1;

    const ESTADO_ELIMINADO = 2;

    const TABLA = 'actividad';

    public function guardar($datos)
    {
        $id = 0;
        if (!empty($datos["id_actividad"])) {
        	$id = (int) $datos["id_actividad"];
        }
        
        unset($datos["id"]);
        $datos = array_intersect_key($datos, array_flip($this->_getCols()));
        
        if ($id > 0) {
        	$cantidad = $this->update($datos, 'id_actividad = ' . $id);
        	$id = ($cantidad < 1) ? 0 : $id;
        } else {
        	$id = $this->insert($datos);
        }
        
        return $id;
    }

    public function combo()
    {
        return $this->getAdapter()->select()->from($this->_name,array('key' => 'id_actividad', 'value' => 'descripcion'))
                ->where('estado = ?',self::ESTADO_ACTIVO)->query()->fetchAll();
    }
    
    public function obtenerActividad($proyecto, $proceso, $nivel)
    {
        return $this->getAdapter()->select()->from($this->_name)
                ->where('id_proyecto = ?', $proyecto)
                ->where('id_proceso = ?', $proceso)
                ->where('nivel = ?', $nivel)
                ->query()->fetchAll();
    }


}

