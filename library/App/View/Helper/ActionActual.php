<?php

/**
 * Description of Util
 *
 * @author svaisman
 */
class App_View_Helper_ActionActual extends Zend_View_Helper_HtmlElement
{
    public function ActionActual()
    {
        return Zend_Controller_Front::getInstance()->getRequest()->getActionName();
    }
}