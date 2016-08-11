<?php

/**
 * Retorna los dias restantes a una determinada fecha agregando el texto 
 * caracteristico determinando si la fecha ya paso o aun no.
 *
 * @author Favio Condori
 */
class App_View_Helper_DiaPublicacionImpreso extends Zend_View_Helper_HtmlElement
{
    private $_dias = array(
        0 => 'Domingo', 1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado'
    );

    public function DiaPublicacionImpreso($medio)
    {
        $config = Zend_Registry::get('config');
        $cierre = $config->cierre->{$medio};

        return $this->_dias[$cierre->dia] . ' ' . 
            ($cierre->hora > 12 ? $cierre->hora - 12 . ':00 pm' : $cierre->hora . ':00 am');
    }

    
}