<?php

class Application_Model_RolRecurso extends Zend_Db_Table
{
    protected $_name = 'rol_recurso';
    protected $_primary = 'id_rolrecurso';
    
    const ESTADO_INACTIVO = 0;
    const ESTADO_ACTIVO = 1;
    CONST ESTADO_ELIMINADO = 2;
    
    const TABLA = 'rol_recurso';
    
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
    
    /*
    Verifica si existe datos de un usuario con su proyecto en la tabla rol_recurso
    */
    public function validaRecursoUsuario($usuario,$proyecto) {
        
        return $this->getAdapter()->select()->from(Application_Model_RolRecurso::TABLA)
                ->where("id_proyecto = ?",$proyecto)
                ->where("id_usuario = ?",$usuario)->query()->fetchAll();
        
    }
    
    //Validar si pertenece a un proyecto
    public function validarUsuario($usuario)
    {
        return $this->getAdapter()->select()->from($this->_name)
                ->where('estado = ?',self::ESTADO_ACTIVO)
                ->where('id_usuario = ?',$usuario)
                ->query()->fetchAll();
        
    }


}

