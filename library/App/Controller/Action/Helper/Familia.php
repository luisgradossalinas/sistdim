<?php

class App_Controller_Action_Helper_Familia extends Zend_Controller_Action_Helper_Abstract {

   
    private $_familia;

 
    public function select($familia, $contador) {
        
        $this->_familia = new Application_Model_Familia;
        $dataFamilia = $this->_familia->combo();
        $select = '<select style="width:90%" id=familia_'.$contador." name=familia_".$contador.">"
                . "<option value=''>[Familia]</option>";
        
        foreach ($dataFamilia as $data) {
            if ($familia == $data['key']) {
                $select .= "<option value='".$data['key']."' selected>".$data['value']."</option>";
            } else {
                $select .= "<option value='".$data['key']."'>".$data['value']."</option>";
            }
        }
        $select .= "</select>";
        
        return $select;
        
    }


}
