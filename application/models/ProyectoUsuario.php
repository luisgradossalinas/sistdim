<?php

class Application_Model_ProyectoUsuario extends Zend_Db_Table
{

    protected $_name = 'proyecto_usuario';

    protected $_primary = 'id_proy_usuario';
    
    const ESTADO_INACTIVO = 0;

    const ESTADO_ACTIVO = 1;

    const ESTADO_ELIMINADO = 2;

    const TABLA = 'proyecto';

    public function guardar($datos)
    {
        $id = 0;
        if (!empty($datos["id_proy_usuario"])) {
        	$id = (int) $datos["id_proy_usuario"];
        }
        
        unset($datos["id_proy_usuario"]);
        $datos = array_intersect_key($datos, array_flip($this->_getCols()));
        
        if ($id > 0) {
        	$this->update($datos, 'id_proy_usuario = ' . $id);
        } else {
        	$id = $this->insert($datos);
        }
        return $id;
    }

    public function listado_()
    {
        return $this->getAdapter()->select()->from($this->_name)->query()->fetchAll();
    }

    
    //Validar si es admin o si pertenece a un proyecto
    public function validarAccesoProyUsuario($usuario,$proyecto)
    {
        return $this->getAdapter()->select()->from(array("a" => $this->_name))
               ->joinInner(array('u' => Application_Model_Usuario::TABLA), 'u.id = a.id_usuario',
                       array())
                ->where('a.estado = ?',self::ESTADO_ACTIVO)
                ->where('a.id_proyecto = ?',$proyecto)
                ->where('a.id_usuario = ?',$usuario)
                ->query()->fetchAll();
        
    }


}

