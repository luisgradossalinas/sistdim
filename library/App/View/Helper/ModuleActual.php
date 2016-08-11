<?php

/**
 * Description of Util
 *
 * @author svaisman
 */
class App_View_Helper_ModuleActual extends Zend_View_Helper_HtmlElement
{
    public function ModuleActual()
    {
        return Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
    }
}