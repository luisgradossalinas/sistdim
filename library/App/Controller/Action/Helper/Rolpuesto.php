<?php

class App_Controller_Action_Helper_Rolpuesto extends Zend_Controller_Action_Helper_Abstract {

   
    private $_rolPuesto;

 
    public function select($grupo, $contador) {
        
        $this->_rolPuesto = new Application_Model_Rolpuesto;
        $dataRPuesto = $this->_rolPuesto->combo();
        $select = '<select style="width:90%" id=rol_'.$contador." name=rol_".$contador.">"
                . "<option value=''>[Rol]</option>";
        
        foreach ($dataRPuesto as $data) {
            if ($grupo == $data['key']) {
                $select .= "<option value='".$data['key']."' selected>".$data['value']."</option>";
            } else {
                $select .= "<option value='".$data['key']."'>".$data['value']."</option>";
            }
        }
        $select .= "</select>";
        
        return $select;
        
    }


}
