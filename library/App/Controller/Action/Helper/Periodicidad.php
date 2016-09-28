<?php

class App_Controller_Action_Helper_Periodicidad extends Zend_Controller_Action_Helper_Abstract {

   
    private $_periodicidad;

    public function select($periodicidad, $contador) {
        
        $this->_periodicidad = new Application_Model_Periocidad;
        $dataPerio = $this->_periodicidad->listado();
        $select = "<select id='periodicidad_" . $contador . "'>".
                "<option value=''>[Seleccione]</option>";
        foreach ($dataPerio as $data) {
            if ($periodicidad == $data['id_periodicidad']) {
                $select .= "<option value='".$data['id_periodicidad'].'|'.$data['valor']."' selected>".$data['descripcion']."</option>";
            } else {
                $select .= "<option value='".$data['id_periodicidad'].'|'.$data['valor']."'>".$data['descripcion']."</option>";
            }
        }
        $select .= "</select>";
        
        return $select;
        
    }


}
