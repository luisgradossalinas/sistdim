<?php

class Application_Model_Proyecto extends Zend_Db_Table
{

    protected $_name = 'proyecto';

    protected $_primary = 'id_proyecto';

    const ESTADO_INACTIVO = 0;

    const ESTADO_ACTIVO = 1;

    const ESTADO_ELIMINADO = 2;

    const TABLA = 'proyecto';

    public function guardar($datos)
    {
        $id = 0;
        if (!empty($datos["id"])) {
        	$id = (int) $datos["id"];
        }
        
        unset($datos["id"]);
        $datos = array_intersect_key($datos, array_flip($this->_getCols()));
        
        if ($id > 0) {
            if (isset($datos['inicio']) && !empty($datos['inicio'])) {
        	$datos['inicio'] = new Zend_Date($datos['inicio'],'yyyy-mm-dd');
        	$datos['inicio'] = $datos['inicio']->get('yyyy-mm-dd');
            }
            if (isset($datos['fin']) && !empty($datos['fin'])) {
        	$datos['fin'] = new Zend_Date($datos['fin'],'yyyy-mm-dd');
        	$datos['fin'] = $datos['fin']->get('yyyy-mm-dd');
            }
        	$cantidad = $this->update($datos, 'id_proyecto = ' . $id);
        	$id = ($cantidad < 1) ? 0 : $id;
        } else {
            if (isset($datos['inicio']) && !empty($datos['inicio'])) {
        	$datos['inicio'] = new Zend_Date($datos['inicio'],'yyyy-mm-dd');
        	$datos['inicio'] = $datos['inicio']->get('yyyy-mm-dd');
            }
            if (isset($datos['fin']) && !empty($datos['fin'])) {
        	$datos['fin'] = new Zend_Date($datos['fin'],'yyyy-mm-dd');
        	$datos['fin'] = $datos['fin']->get('yyyy-mm-dd');
            }
        	$id = $this->insert($datos);
        }
        
        return $id;
    }

    public function listado_()
    {
        return $this->getAdapter()->select()->from($this->_name)->query()->fetchAll();
    }

    public function listado()
    {
        return $this->getAdapter()->select()->from(array("a" => $this->_name))
               ->joinInner(array('b' => Application_Model_Entidad::TABLA), 'b.id_entidad = a.id_entidad',
                       array('entidad'=> 'b.nombre'))
                ->where('a.estado != ?',self::ESTADO_ELIMINADO)
                ->query()->fetchAll();
        
    }
    
    public function proyectosActivos()
    {
        return $this->getAdapter()->select()->from(array("a" => $this->_name))
               ->joinInner(array('b' => Application_Model_Entidad::TABLA), 'b.id_entidad = a.id_entidad',
                       array('entidad'=> 'b.nombre'))
                ->where('a.estado = ?',self::ESTADO_ACTIVO)
                ->query()->fetchAll();
        
    }


}

