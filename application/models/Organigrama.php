<?php

class Application_Model_Organigrama extends Zend_Db_Table
{

    protected $_name = 'organigrama';

    protected $_primary = 'id_organigrama';

    const ESTADO_INACTIVO = 0;

    const ESTADO_ACTIVO = 1;

    const ESTADO_ELIMINADO = 2;

    const TABLA = 'organigrama';

    public function guardar($datos)
    {
        $id = 0;
        if (!empty($datos["id"])) {
        	$id = (int) $datos["id"];
        }
        
        unset($datos["id"]);
        $datos = array_intersect_key($datos, array_flip($this->_getCols()));
        
        if ($id > 0) {
        	$cantidad = $this->update($datos, 'id_organigrama = ' . $id);
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
    
    /*
    Obtiene todos los órganos que tiene un proyecto
    */
    function obtenerNaturalezaOrgano($proyecto) {
        
        return $this->getAdapter()->select()->from(array('o' => self::TABLA),array('id_organigrama','organo'))
                ->joinInner(array('n' => Application_Model_Natuorganica::TABLA), 
                        'n.codigo_natuorganica = o.codigo_natuorganica',
                        array('naturaleza' => 'descripcion'))
                ->where('o.id_proyecto = ?', $proyecto)->query()->fetchAll();
        
        
    }

}


