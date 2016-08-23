<?php

class App_View_Helper_Organo extends Zend_View_Helper_HtmlElement
{
    
    private $_organigrama;
    
    public function Organo($id, $contador)
    {
        
        $this->_organigrama = new Application_Model_Organigrama;
        $data = $this->_organigrama->obtenerOrganoProyecto($id);
        $organo = "organo_".$contador;
        
        $html = '';
        $html .= "<select id=".$organo." name=".$organo.">";
        foreach ($data as $value) {
            
            if ($id == $value['organo']) {
                $html .= "<option value='".$value['organo']."' selected>".$value['organo']."</option>";
            } else {
                $html .= "<option value='".$value['codigo_natuorganica']."'>".$value['descripcion']."</option>";
            }
        }
        
        $html .= "</select>";
        return $html;
        
    }
    
}