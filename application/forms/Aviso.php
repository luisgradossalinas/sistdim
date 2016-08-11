<?php

class Application_Form_Aviso extends Zend_Form
{

    public function init()
    {
        $this->setAttrib('id', 'form');
        
        $puesto = new Zend_Form_Element_Text('puesto');
        $puesto->setLabel('Puesto:');
        $puesto->setAttrib('maxlength',50);
        $puesto->addFilter('StripTags');
        $this->addElement($puesto);
        
        $visitas = new Zend_Form_Element_Text('visitas');
        $visitas->setLabel('Visitas:');
        $visitas->addValidator(new Zend_Validate_Int());
        $visitas->setAttrib('maxlength',9);
        $visitas->setAttrib('class','v_numeric');
        $visitas->addFilter('StripTags');
        $this->addElement($visitas);
        
        $fecha_pub = new Zend_Form_Element_Text('fecha_pub');
        $fecha_pub->setLabel('Fecha_pub:');
        $fecha_pub->addValidator(new Zend_Validate_Date('DD-MM-YYYY'));
        $fecha_pub->setAttrib('maxlength',10);
        $fecha_pub->setAttrib('class','v_datepicker');
        $fecha_pub->addFilter('StripTags');
        $this->addElement($fecha_pub);
    }

    public function populate($data)
    {
        $data['fecha_pub'] = new Zend_Date($data['fecha_pub'],'yyyy-mm-dd');
        $data['fecha_pub'] = $data['fecha_pub']->get('dd/mm/yyyy');
        return $this->setDefaults($data);
    }


}

