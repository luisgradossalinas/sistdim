<?php

class Application_Model_Generator extends Zend_Db_Table
{
    protected $_name = 'dinamic';
    
    const ESTADO_INACTIVO = 0;
    const ESTADO_ACTIVO = 1;
    CONST ESTADO_ELIMINADO = 2;
    
    const TABLA = 'dinamic';
    
    public function columnas($tabla)
    {
        
        $db = $this->getAdapter();
        return $db->describeTable($tabla);
        
        
    }      
    
    public function listaTablas()
    {
        
        $db = $this->getAdapter();
        $data = $db->query('show tables')->fetchAll();
        
        $d = array();
        $contador = 0;
        
        foreach ($data as $key => $value)
            foreach ($value  as $k)
            {
            
                $d[] = array('key' => $contador, 'value' => $k);
                $contador ++;
            }
                
        
        return $d;
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
    
    public function listado() {
        
        return $this->getAdapter()->select()->from($this->_name)->query()->fetchAll();
        
    }


}

