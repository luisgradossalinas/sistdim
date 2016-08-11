<?php

/**
 * Description of Util
 *
 * @author svaisman
 */
class App_View_Helper_Existepalabra extends Zend_View_Helper_HtmlElement
{

    public function Existepalabra($nueva, $actual, $separador)
    {
        return in_array($nueva, explode($separador, $actual));
    }
    
}