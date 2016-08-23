<?php

class App_View_Helper_Naturaleza extends Zend_View_Helper_HtmlElement
{
    
    private $_naturalezaOrganica;
    
    public function Naturaleza($id, $contador)
    {
        
        $this->_naturalezaOrganica = new Application_Model_Natuorganica;
        $data = $this->_naturalezaOrganica->listado();
        $natu = "naturaleza_".$contador;
        
        $html = '';
        $html .= "<select id=".$natu." name=".$natu.">";
        foreach ($data as $value) {
            
            if ($id == $value['codigo_natuorganica']) {
                $html .= "<option value='".$value['codigo_natuorganica']."' selected>".$value['descripcion']."</option>";
            } else {
                $html .= "<option value='".$value['codigo_natuorganica']."'>".$value['descripcion']."</option>";
            }
        }
        
        $html .= "</select>";
        return $html;
        
    }
    
}