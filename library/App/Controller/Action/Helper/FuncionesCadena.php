<?php
class App_Controller_Action_Helper_FuncionesCadena extends Zend_Controller_Action_Helper_Abstract
{

    protected $_cache = null;

    public function __construct()
    {

    }
    
    public function mostrarFechaSinHora($fecha)
    {
        return date("d/m/Y", $fecha->getTimestamp());
    }
}

