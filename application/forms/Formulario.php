<?php

class Application_Form_Tabla extends Zend_Form
{

    public function init()
    {
        $this->setAttrib('id', 'form');
        $nombre = new Zend_Form_Element_Text('nombre');
    }


}

