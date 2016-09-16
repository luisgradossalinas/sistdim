<?php

class App_Controller_Action_Helper_Puesto extends Zend_Controller_Action_Helper_Abstract {

   
    private $_puesto;
 
    public function select($unidad, $puesto, $contador, $tarea = '') {
        
        $this->_puesto = new Application_Model_Puesto;
        $dataPuesto = $this->_puesto->puestosActividades($unidad, null);
        $select = "<div id='".$tarea."capa_".$contador."'><select id='".$tarea."puesto_" . $contador . "'>".
                "<option value=''>[Seleccione puesto]</option>";
        foreach ($dataPuesto as $data) {
            if ($puesto == $data['id_puesto']) {
                $select .= "<option value='".$data['id_puesto']."' selected>".$data['descripcion']."</option>";
            } else {
                $select .= "<option value='".$data['id_puesto']."'>".$data['descripcion']."</option>";
            }
        }
        $select .= "</select></div>";
        
        return $select;
        
    }


}
