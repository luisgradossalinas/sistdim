<?php

class Application_Form_Entidad extends Zend_Form
{

    public function init()
    {
        $this->setAttrib('id', 'form');
        
        $nombre = new Zend_Form_Element_Text('nombre');
        $nombre->setLabel('Entidad:');
        $nombre->setAttrib('maxlength',100);
        $nombre->setRequired();
        $nombre->addFilter('StripTags');
        $this->addElement($nombre);
        
        $descripcion = new Zend_Form_Element_Text('descripcion');
        $descripcion->setLabel('Descripcion:');
        $descripcion->setAttrib('maxlength',500);
        $descripcion->addFilter('StripTags');
        $this->addElement($descripcion);
        
        $ruc = new Zend_Form_Element_Text('ruc');
        $ruc->setLabel('Ruc:');
        $ruc->setAttrib('maxlength',11);
        $ruc->setAttrib('class','v_numeric');
        $ruc->addFilter('StripTags');
        $this->addElement($ruc);
        
        $telefono = new Zend_Form_Element_Text('telefono');
        $telefono->setLabel('Telefono:');
        $telefono->setAttrib('maxlength',18);
        $telefono->addFilter('StripTags');
        $this->addElement($telefono);
        
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

