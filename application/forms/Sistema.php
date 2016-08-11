<?php

class Application_Form_Sistema extends Zend_Form
{

    public function init()
    {
        $this->setAttrib('id', 'form');
        
        $nombre = new Zend_Form_Element_Text('nombre');
        $nombre->setLabel('Nombre:');
        $nombre->setRequired();
        $nombre->setAttrib('maxlength',50);
        $nombre->addFilter('StripTags');
        $this->addElement($nombre);
        
        $costo = new Zend_Form_Element_Text('costo');
        $costo->setLabel('Costo:');
        $costo->addValidator(new Zend_Validate_Float());
        $costo->setAttrib('maxlength',10);
        $costo->setAttrib('class','v_decimal');
        $costo->addFilter('StripTags');
        $this->addElement($costo);
        
        $c_tinyint = new Zend_Form_Element_Text('c_tinyint');
        $c_tinyint->setLabel('C_tinyint:');
        $c_tinyint->addValidator(new Zend_Validate_Int());
        $c_tinyint->setAttrib('maxlength',3);
        $c_tinyint->setAttrib('size',5);
        $c_tinyint->setAttrib('class','v_numeric');
        $c_tinyint->addFilter('StripTags');
        $this->addElement($c_tinyint);
        
        $c_mediumint = new Zend_Form_Element_Text('c_mediumint');
        $c_mediumint->setLabel('C_mediumint:');
        $c_mediumint->addValidator(new Zend_Validate_Int());
        $c_mediumint->setAttrib('maxlength',7);
        $c_mediumint->setAttrib('class','v_numeric');
        $c_mediumint->addFilter('StripTags');
        $this->addElement($c_mediumint);
        
        $c_int = new Zend_Form_Element_Text('c_int');
        $c_int->setLabel('C_int:');
        $c_int->addValidator(new Zend_Validate_Int());
        $c_int->setAttrib('maxlength',9);
        $c_int->setAttrib('class','v_numeric');
        $c_int->addFilter('StripTags');
        $this->addElement($c_int);
        
        $c_bigint = new Zend_Form_Element_Text('c_bigint');
        $c_bigint->setLabel('C_bigint:');
        $c_bigint->addValidator(new Zend_Validate_Int());
        $c_bigint->setAttrib('maxlength',17);
        $c_bigint->setAttrib('class','v_numeric');
        $c_bigint->addFilter('StripTags');
        $this->addElement($c_bigint);
        
        $tiempo = new Zend_Form_Element_Text('tiempo');
        $tiempo->setLabel('Tiempo:');
        $tiempo->setRequired();
        $tiempo->addValidator(new Zend_Validate_Int());
        $tiempo->setAttrib('maxlength',9);
        $tiempo->setAttrib('class','v_numeric');
        $tiempo->addFilter('StripTags');
        $this->addElement($tiempo);
    }


}

