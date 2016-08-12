<?php

class Application_Form_Rolpuesto extends Zend_Form
{

    public function init()
    {
        $this->setAttrib('id', 'form');
        
        $descripcion = new Zend_Form_Element_Text('descripcion');
        $descripcion->setLabel('Descripcion:');
        $descripcion->setRequired();
        $descripcion->setAttrib('maxlength',100);
        $descripcion->addFilter('StripTags');
        $this->addElement($descripcion);
        
        $estado = new Zend_Form_Element_Select('estado');
        $estado->setLabel('Estado:');
        $estado->setRequired();
        $estado->setMultiOptions(array('1' => 'Activo', '0' => 'Inactivo'));
        $estado->addFilter('StripTags');
        $this->addElement($estado);
        
        $codigo_familia = new Zend_Form_Element_Text('codigo_familia');
        $codigo_familia->setLabel('Codigo_familia:');
        $codigo_familia->setRequired();
        $codigo_familia->addValidator(new Zend_Validate_Int());
        $codigo_familia->setAttrib('maxlength',9);
        $codigo_familia->setAttrib('class','v_numeric');
        $codigo_familia->addFilter('StripTags');
        $this->addElement($codigo_familia);
    }

    public function populate($data)
    {
        return $this->setDefaults($data);
    }


}

