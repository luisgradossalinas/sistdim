<?php

class Application_Form_Calidad extends Zend_Form
{

    public function init()
    {
        $this->setAttrib('id', 'form');
        
        $nombre = new Zend_Form_Element_Text('nombre');
        $nombre->setLabel('Nombre:');
        $nombre->setAttrib('maxlength',100);
        $nombre->addFilter('StripTags');
        $this->addElement($nombre);
        
        $fecha = new Zend_Form_Element_Text('fecha');
        $fecha->setLabel('Fecha:');
        $fecha->addValidator(new Zend_Validate_Date('DD-MM-YYYY'));
        $fecha->setAttrib('maxlength',10);
        $fecha->setAttrib('class','v_datepicker');
        $fecha->addFilter('StripTags');
        $this->addElement($fecha);
    }

    public function populate($data)
    {
        $data['fecha'] = new Zend_Date($data['fecha'],'yyyy-mm-dd');
        $data['fecha'] = $data['fecha']->get('dd/mm/yyyy');
        return $this->setDefaults($data);
    }


}

