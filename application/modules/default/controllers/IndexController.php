<?php

class Default_IndexController extends App_Controller_Action_Admin
{
    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
        $this->_redirect('admin');
    }

}





