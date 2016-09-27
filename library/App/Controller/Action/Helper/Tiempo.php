<?php

class App_Controller_Action_Helper_Tiempo extends Zend_Controller_Action_Helper_Abstract {

   
    private $_tiempo;

 
    public function select($tiempo, $contador) {
        
        $this->_tiempo = new Application_Model_Tiempo;
        $dataTiempo = $this->_tiempo->listado();
        $select = "<select id='tiempo_" . $contador . "'>".
                "<option value=''>[Seleccione]</option>";
        foreach ($dataTiempo as $data) {
            if ($tiempo == $data['id_tiempo']) {
                $select .= "<option value='".$data['id_tiempo']."' selected>".$data['descripcion']."</option>";
            } else {
                $select .= "<option value='".$data['id_tiempo']."'>".$data['descripcion']."</option>";
            }
        }
        $select .= "</select>";
        
        return $select;
        
    }


}
