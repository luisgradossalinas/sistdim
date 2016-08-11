<?php

class Application_Form_Tema extends Zend_Form
{

    public function init()
    {
        $this->setAttrib('id', 'form');
        
        $descripcion = new Zend_Form_Element_Text('descripcion');
        $descripcion->setLabel('Descripcion:');
        $descripcion->setAttrib('maxlength',100);
        $descripcion->addFilter('StripTags');
        $this->addElement($descripcion);
        
        $duracion = new Zend_Form_Element_Text('duracion');
        $duracion->setLabel('Duracion:');
        $duracion->setAttrib('maxlength',50);
        $duracion->addFilter('StripTags');
        $this->addElement($duracion);
        
        $fecha_inicio = new Zend_Form_Element_Text('fecha_inicio');
        $fecha_inicio->setLabel('Fecha_inicio:');
        $fecha_inicio->addValidator(new Zend_Validate_Date('DD-MM-YYYY'));
        $fecha_inicio->setAttrib('maxlength',10);
        $fecha_inicio->setAttrib('class','v_datepicker');
        $fecha_inicio->addFilter('StripTags');
        $this->addElement($fecha_inicio);
    }
    


}

