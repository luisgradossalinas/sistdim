<?php

class App_Controller_Action_Helper_Categoriapuesto extends Zend_Controller_Action_Helper_Abstract {

   
    private $_categoriaPuesto;

    public function select($familia, $categoria, $contador) {
        
        $this->_categoriaPuesto = new Application_Model_Categoriapuesto;
        $dataCat = $this->_categoriaPuesto->obtenerCategoria($familia);
        $select = '<select style="width:100%" id=cat_'.$contador." name=cat_".$contador.">"
                . "<option value=''>[Categoría]</option>";
        
        foreach ($dataCat as $data) {
            if ($categoria == $data['id_categoria_puesto']) {
                $select .= "<option value='".$data['id_categoria_puesto']."' selected>".$data['descripcion']."</option>";
            } else {
                $select .= "<option value='".$data['id_categoria_puesto']."'>".$data['descripcion']."</option>";
            }
        }
        $select .= "</select>";
        
        return $select;
        
    }


}
