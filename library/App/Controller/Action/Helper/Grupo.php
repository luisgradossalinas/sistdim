<?php

class App_Controller_Action_Helper_Grupo extends Zend_Controller_Action_Helper_Abstract {

   
    private $_grupo;

 
    public function select($grupo, $contador) {
        
        $this->_grupo = new Application_Model_Grupo;
        $dataGrupo = $this->_grupo->combo();
        $select = '<select style="width:90%" id=grupo_'.$contador." name=grupo_".$contador.">"
                . "<option value=''>[Grupo]</option>";
        
        foreach ($dataGrupo as $data) {
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
