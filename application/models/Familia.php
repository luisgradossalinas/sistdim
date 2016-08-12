<?php

class Application_Model_Familia extends Zend_Db_Table
{

    protected $_name = 'familia';

    protected $_primary = 'codigo_familia';

    const ESTADO_INACTIVO = 0;

    const ESTADO_ACTIVO = 1;

    const ESTADO_ELIMINADO = 2;

    const TABLA = 'familia';

    public function guardar($datos)
    {
        $id = 0;
        if (!empty($datos["id"])) {
        	$id = (int) $datos["id"];
        }
        
        unset($datos["id"]);
        $datos = array_intersect_key($datos, array_flip($this->_getCols()));
        
        if ($id > 0) {
        	$cantidad = $this->update($datos, 'codigo_familia = ' . $id);
        	$id = ($cantidad < 1) ? 0 : $id;
        } else {
        	$id = $this->insert($datos);
        }
        
        return $id;
    }

    public function listado()
    {
        return $this->getAdapter()->select()->from(array("f" =>$this->_name))
                ->joinInner(array('g' => Application_Model_Grupo::TABLA), 
                        'g.codigo_grupo = f.codigo_grupo',array("nom_grupo" => "g.descripcion"))
                ->query()->fetchAll();
    }


}

