<?php

class Application_Form_Natuorganica extends Zend_Form
{

    public function init()
    {
        $this->setAttrib('id', 'form');
        
        $descripcion = new Zend_Form_Element_Text('descripcion');
        $descripcion->setLabel('Descripcion:');
        $descripcion->setAttrib('maxlength',100);
        $descripcion->addFilter('StripTags');
        $this->addElement($descripcion);
        
        $estado = new Zend_Form_Element_Select('estado');
        $estado->setLabel('Estado:');
        $estado->setRequired();
        $estado->setMultiOptions(array('1' => 'Activo', '0' => 'Inactivo'));
        $estado->addFilter('StripTags');
        $this->addElement($estado);
        
    }

    public function populate($data)
    {
        return $this->setDefaults($data);
    }


}

