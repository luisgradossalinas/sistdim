<?php

class Application_Form_Casa extends Zend_Form
{

    public function init()
    {
        $this->setAttrib('id', 'form');
        
        $nombre = new Zend_Form_Element_Text('nombre');
        $nombre->setLabel('Nombre:');
        $nombre->setAttrib('maxlength',100);
        $nombre->addFilter('StripTags');
        $this->addElement($nombre);
        
        $area = new Zend_Form_Element_Text('area');
        $area->setLabel('Area:');
        $area->setAttrib('maxlength',5);
        $area->addFilter('StripTags');
        $this->addElement($area);
        
        $dueno = new Zend_Form_Element_Text('dueno');
        $dueno->setLabel('Dueno:');
        $dueno->setAttrib('maxlength',100);
        $dueno->addFilter('StripTags');
        $this->addElement($dueno);
    }


}

