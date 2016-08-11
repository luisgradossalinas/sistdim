<?php
class App_View_Helper_FechaMostrar extends Zend_View_Helper_HtmlElement
{
    
    const DEFAULT_DATETIME = '0000-00-00 00:00:00';
    const DEFAULT_DATE = '0000-00-00';
    
    public function FechaMostrar($fecha)
    {
        
        if ($fecha == self::DEFAULT_DATE || $fecha == self::DEFAULT_DATETIME) {
            return '';
        }
        
        $fecha = new Zend_Date($fecha);
        
        return date("d/m/Y", $fecha->getTimestamp());
    }

}

