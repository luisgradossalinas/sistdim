<?php

class App_Controller_Action_Helper_Unidadorganica extends Zend_Controller_Action_Helper_Abstract {

   
    private $_unidadOrganica;

 
    public function select($proyecto, $unidad, $contador) {
        
        $this->_unidadOrganica = new Application_Model_UnidadOrganica;
        $dataUnidad = $this->_unidadOrganica->obtenerUOrganica($proyecto, null);
        $select = "<select id='unidad_" . $contador . "'>".
                "<option value=''>[Seleccione unidad orgánica]</option>";
        foreach ($dataUnidad as $data) {
            if ($unidad == $data['id_uorganica']) {
                $select .= "<option value='".$data['id_uorganica']."' selected>".$data['descripcion']."</option>";
            } else {
                $select .= "<option value='".$data['id_uorganica']."'>".$data['descripcion']."</option>";
            }
        }
        $select .= "</select>";
        
        return $select;
        
    }


}
