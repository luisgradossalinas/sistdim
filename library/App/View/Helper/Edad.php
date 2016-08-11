<?php

/**
 * Description of Edad
 *
 * @author dpozo
 */
class App_View_Helper_Edad extends Zend_View_Helper_HtmlElement
{
    public function Edad($fechaNacimiento)
    {
        $fi = new DateTime("now");
        $ff = new DateTime($fechaNacimiento);
        return $ff->diff($fi)->format('%y');
    }
    
}