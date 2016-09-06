<?php

class App_Controller_Action_Helper_Tipoproceso extends Zend_Controller_Action_Helper_Abstract {

   
    private $_tipoProceso;

 
    public function select($tipo, $contador) {
        
        $this->_tipoProceso = new Application_Model_Tipoproceso;
        $dataTipo = $this->_tipoProceso->listado();
        $select = '<select style="width:100%" id=tipoproceso_'.$contador." name=tipoproceso_".$contador.">"
                . "<option value=''>[Tipo]</option>";
        
        foreach ($dataTipo as $data) {
            if ($tipo == $data['codigo_tipoproceso']) {
                $select .= "<option value='".$data['codigo_tipoproceso']."' selected>".$data['descripcion']."</option>";
            } else {
                $select .= "<option value='".$data['codigo_tipoproceso']."'>".$data['descripcion']."</option>";
            }
        }
        $select .= "</select>";
        
        return $select;
        
    }


}
