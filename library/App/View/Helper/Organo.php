<?php

class App_View_Helper_Organo extends Zend_View_Helper_HtmlElement
{
    
    private $_organo;
    
    public function Organo($organo, $proyecto, $contador)
    {
        //$this->Organo($value['id_organo'],$value['id_proyecto'],$contador)
        $this->_organo = new Application_Model_Organo;
        $data = $this->_organo->obtenerOrganoProyecto($proyecto);
        $organoDes = "organo_".$contador;
        
        $html = '';
        $html .= "<select id=".$organoDes." name=".$organoDes.">";
        foreach ($data as $value) {
            
            if ($organo == $value['id_organo']) {
                $html .= "<option value='".$value['id_organo']."' selected>".$value['organo']."</option>";
            } else {
                $html .= "<option value='".$value['id_organo']."'>".$value['organo']."</option>";
            }
        }
        
        $html .= "</select>";
        return $html;
        
    }
    
}