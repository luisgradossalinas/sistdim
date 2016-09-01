<?php

class App_Controller_Action_Helper_Familia extends Zend_Controller_Action_Helper_Abstract {

   
    private $_familia;

 
    public function select($grupo, $familia, $contador) {
        
        $this->_familia = new Application_Model_Familia;
        $dataFamilia = $this->_familia->obtenerFamilias($grupo);
        $select = '<select style="width:90%" id=familia_'.$contador." name=familia_".$contador.">"
                . "<option value=''>[Familia]</option>";
        
        foreach ($dataFamilia as $data) {
            if ($familia == $data['codigo_familia']) {
                $select .= "<option value='".$data['codigo_familia']."' selected>".$data['descripcion']."</option>";
            } else {
                $select .= "<option value='".$data['codigo_familia']."'>".$data['descripcion']."</option>";
            }
        }
        $select .= "</select>";
        
        return $select;
        
    }


}
