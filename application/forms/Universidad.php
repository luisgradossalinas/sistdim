<?php

class Application_Form_Universidad extends Zend_Form
{

    public function init()
    {
        $this->setAttrib('id', 'form');
        
        $nombre = new Zend_Form_Element_Text('nombre');
        $nombre->setLabel('Nombre:');
        $nombre->setRequired();
        $nombre->setAttrib('maxlength',100);
        $nombre->addFilter('StripTags');
        $this->addElement($nombre);
        
        $direccion = new Zend_Form_Element_Text('direccion');
        $direccion->setLabel('Direccion:');
        $direccion->setAttrib('maxlength',200);
        $direccion->addFilter('StripTags');
        $this->addElement($direccion);
        
        $telefono = new Zend_Form_Element_Text('telefono');
        $telefono->setLabel('Telefono:');
        $telefono->addValidator(new Zend_Validate_Int());
        $telefono->setAttrib('maxlength',9);
        $telefono->setAttrib('class','v_numeric');
        $telefono->addFilter('StripTags');
        $this->addElement($telefono);
    }


}

