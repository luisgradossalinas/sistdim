<?php

class Application_Model_Rolpuesto extends Zend_Db_Table
{

    protected $_name = 'rolpuesto';

    protected $_primary = 'codigo_rol_puesto';

    const ESTADO_INACTIVO = 0;

    const ESTADO_ACTIVO = 1;

    const ESTADO_ELIMINADO = 2;

    const TABLA = 'rolpuesto';

    public function guardar($datos)
    {
        $id = 0;
        if (!empty($datos["id"])) {
        	$id = (int) $datos["id"];
        }
        
        unset($datos["id"]);
        $datos = array_intersect_key($datos, array_flip($this->_getCols()));
        
        if ($id > 0) {
        	$cantidad = $this->update($datos, 'codigo_rol_puesto = ' . $id);
        	$id = ($cantidad < 1) ? 0 : $id;
        } else {
        	$id = $this->insert($datos);
        }
        
        return $id;
    }

    public function listado()
    {
        return $this->getAdapter()->select()->from(array("rp" =>$this->_name))
        ->joinInner(array("f" => Application_Model_Familia::TABLA),"f.codigo_familia = rp.codigo_familia",array("nom_familia" => 'f.descripcion'))
        ->query()->fetchAll();
    }


}

