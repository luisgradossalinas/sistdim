<?php


class App_View_Helper_ViewCache extends Zend_View_Helper_HtmlElement
{

    public function ViewCache()
    {
        return Zend_Registry::get('outputCache');
    }

}