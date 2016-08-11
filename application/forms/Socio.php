<?php

class Application_Form_Socio extends Zend_Form
{

    public function init()
    {
        $this->setAttrib('id', 'form');
        
        $nombre = new Zend_Form_Element_Text('nombre');
        $nombre->setLabel('Nombre:');
        $nombre->setAttrib('maxlength',200);
        $nombre->addFilter('StripTags');
        $this->addElement($nombre);
        
        $nacimiento = new Zend_Form_Element_Text('nacimiento');
        $nacimiento->setLabel('Nacimiento:');
        $nacimiento->addValidator(new Zend_Validate_Date('DD-MM-YYYY'));
        $nacimiento->setAttrib('maxlength',10);
        $nacimiento->setAttrib('class','v_datepicker');
        $nacimiento->addFilter('StripTags');
        $this->addElement($nacimiento);
        
        $sueldo = new Zend_Form_Element_Text('sueldo');
        $sueldo->setLabel('Sueldo:');
        $sueldo->addFilter('StripTags');
        $this->addElement($sueldo);
    }

    public function populate($data)
    {
        $data['nacimiento'] = new Zend_Date($data['nacimiento'],'yyyy-mm-dd');
        $data['nacimiento'] = $data['nacimiento']->get('dd/mm/yyyy');
        return $this->setDefaults($data);
    }


}

