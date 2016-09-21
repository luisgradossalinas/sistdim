<?php

class App_Controller_Action_Helper_Nivelpuesto extends Zend_Controller_Action_Helper_Abstract {

   
    private $_nivelPuesto;

 
    public function select($grupo, $nivel, $contador) {
        
        $this->_nivelPuesto = new Application_Model_Nivelpuesto;
        $dataNPuesto = $this->_nivelPuesto->obtenerNiveles($grupo);
        $select = '<select style="width:100%" id=npuesto_'.$contador." name=npuesto_".$contador.">"
                . "<option value=''>[Nivel]</option>";
        
        foreach ($dataNPuesto as $data) {
            if ($nivel == $data['id_nivel_puesto']) {
                $select .= "<option value='".$data['id_nivel_puesto']."' selected>".$data['descripcion']."</option>";
            } else {
                $select .= "<option value='".$data['id_nivel_puesto']."'>".$data['descripcion']."</option>";
            }
        }
        $select .= "</select>";
        
        return $select;
        
    }


}
