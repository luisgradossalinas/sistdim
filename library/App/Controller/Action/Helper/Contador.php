<?php

class App_Controller_Action_Helper_Contador 
    extends Zend_Controller_Action_Helper_Abstract
{
    public function contarPalabras($palabras)
    {
        $separate = new App_Filter_Separate();
        $palabras = trim($palabras);
        $palabras = $separate->filter($palabras);
        while (strpos($palabras, "  ")) {
            $palabras = str_replace("  ", " ", $palabras);
        }
        $cant = 0;
        foreach (explode(" ", $palabras) as $p) {
            $cant++;
        }
        return $cant;
    }
    
    public function contadorPalabraText($palabras, $cantPalabras)
    {
        
        $particionado = explode(' ', $palabras);
        $con = 0 ;
        $val = '';
        $options = func_get_args();
        
        if (isset ($options[2])) {
            $palabras = $options[2];
        }
        
        if (isset ($options[3])) {
            $cantPalabras = $options[3];
        }
        
        foreach ($particionado as $data) {
            if ($data!='') {
                $con = $con+1;
            }
        }
        
        if ($con <= $cantPalabras) {
            return true;
        } else {
            return false;
        }
    }
    
}