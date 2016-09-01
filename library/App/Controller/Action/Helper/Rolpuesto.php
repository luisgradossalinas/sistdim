<?php

class App_Controller_Action_Helper_Rolpuesto extends Zend_Controller_Action_Helper_Abstract {

   
    private $_rolPuesto;

 
    public function select($familia, $rol, $contador) {
        
        $this->_rolPuesto = new Application_Model_Rolpuesto;
        $dataRPuesto = $this->_rolPuesto->obtenerRoles($familia);
        $select = '<select style="width:90%" id=rol_'.$contador." name=rol_".$contador.">"
                . "<option value=''>[Rol]</option>";
        
        foreach ($dataRPuesto as $data) {
            if ($rol == $data['codigo_rol_puesto']) {
                $select .= "<option value='".$data['codigo_rol_puesto']."' selected>".$data['descripcion']."</option>";
            } else {
                $select .= "<option value='".$data['codigo_rol_puesto']."'>".$data['descripcion']."</option>";
            }
        }
        $select .= "</select>";
        
        return $select;
        
    }


}
