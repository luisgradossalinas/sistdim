<?php

/**
 * Retorna la cantidad de meses u años segun sea el caso
 *
 * @author jfabian
 */
class App_View_Helper_Meses extends Zend_View_Helper_HtmlElement
{
    public function Meses($meses)
    {
        if ($meses == 0) {
            return false;
        }
        if ($meses >= 12) {
            $anios = (int) ($meses/12);
            if ($anios == 1) {
                return $anios.' año';
            } else {
                return $anios.' años';
            }
        } else {
            return $meses.' meses';
        }
    }
    
}