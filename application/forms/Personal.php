<?php

class Application_Form_Personal extends Zend_Form
{

    public function init()
    {
        $this->setAttrib('id', 'form');
        
        $nombre = new Zend_Form_Element_Text('nombre');
        $nombre->setLabel('Nombre:');
        $nombre->setRequired();
        $nombre->setAttrib('maxlength',51);
        $nombre->addFilter('StripTags');
        $this->addElement($nombre);
        
        $fecha_nacimiento = new Zend_Form_Element_Text('fecha_nacimiento');
        $fecha_nacimiento->setLabel('Fecha_nacimiento:');
        $fecha_nacimiento->addValidator(new Zend_Validate_Date());
        $fecha_nacimiento->setAttrib('maxlength',10);
        $fecha_nacimiento->setAttrib('class','v_datepicker');
        $fecha_nacimiento->addFilter('StripTags');
        $this->addElement($fecha_nacimiento);
        
        $sueldo = new Zend_Form_Element_Text('sueldo');
        $sueldo->setLabel('Sueldo:');
        $sueldo->setRequired();
        $sueldo->addValidator(new Zend_Validate_Float());
        $sueldo->setAttrib('maxlength',10);
        $sueldo->setAttrib('class','v_decimal');
        $sueldo->addFilter('StripTags');
        $this->addElement($sueldo);
        
        $hijos = new Zend_Form_Element_Text('hijos');
        $hijos->setLabel('Hijos:');
        $hijos->addValidator(new Zend_Validate_Int());
        $hijos->setAttrib('maxlength',9);
        $hijos->setAttrib('class','v_numeric');
        $hijos->addFilter('StripTags');
        $this->addElement($hijos);
        
        $casado = new Zend_Form_Element_Text('casado');
        $casado->setLabel('Casado:');
        $casado->addValidator(new Zend_Validate_Int());
        $casado->setAttrib('maxlength',3);
        $casado->setAttrib('size',5);
        $casado->setAttrib('class','v_numeric');
        $casado->addFilter('StripTags');
        $this->addElement($casado);
        
        $estado = new Zend_Form_Element_Text('estado');
        $estado->setLabel('Estado:');
        $estado->addValidator(new Zend_Validate_Int());
        $estado->setAttrib('maxlength',3);
        $estado->setAttrib('size',5);
        $estado->setAttrib('class','v_numeric');
        $estado->addFilter('StripTags');
        $this->addElement($estado);
    }


}

