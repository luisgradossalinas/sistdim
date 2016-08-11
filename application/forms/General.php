<?php

class Application_Form_General extends Zend_Form
{

    public function init()
    {
        $this->setAttrib('id', 'form');
        
        $demo1 = new Zend_Form_Element_Text('demo1');
        $demo1->setLabel('Demo1:');
        $demo1->setAttrib('maxlength',50);
        $demo1->addFilter('StripTags');
        $this->addElement($demo1);
        
        $fecha = new Zend_Form_Element_Text('fecha');
        $fecha->setLabel('Fecha:');
        $fecha->addValidator(new Zend_Validate_Date('DD-MM-YYYY'));
        $fecha->setAttrib('maxlength',10);
        $fecha->setAttrib('class','v_datepicker');
        $fecha->addFilter('StripTags');
        $this->addElement($fecha);
        
        $telefono = new Zend_Form_Element_Text('telefono');
        $telefono->setLabel('Telefono:');
        $telefono->addValidator(new Zend_Validate_Int());
        $telefono->setAttrib('maxlength',9);
        $telefono->setAttrib('class','v_numeric');
        $telefono->addFilter('StripTags');
        $this->addElement($telefono);
        
        $direccion = new Zend_Form_Element_Text('direccion');
        $direccion->setLabel('Direccion:');
        $direccion->setAttrib('maxlength',150);
        $direccion->addFilter('StripTags');
        $this->addElement($direccion);
        
        $correo = new Zend_Form_Element_Text('correo');
        $correo->setLabel('Correo:');
        $correo->setAttrib('maxlength',150);
        $correo->addFilter('StripTags');
        $this->addElement($correo);
        
        $trabajo = new Zend_Form_Element_Text('trabajo');
        $trabajo->setLabel('Trabajo:');
        $trabajo->setAttrib('maxlength',50);
        $trabajo->addFilter('StripTags');
        $this->addElement($trabajo);
        
        $sueldo = new Zend_Form_Element_Text('sueldo');
        $sueldo->setLabel('Sueldo:');
        $sueldo->addFilter('StripTags');
        $this->addElement($sueldo);
        
        $pais = new Zend_Form_Element_Text('pais');
        $pais->setLabel('Pais:');
        $pais->setAttrib('maxlength',100);
        $pais->addFilter('StripTags');
        $this->addElement($pais);
        
        $hijos = new Zend_Form_Element_Text('hijos');
        $hijos->setLabel('Hijos:');
        $hijos->addValidator(new Zend_Validate_Int());
        $hijos->setAttrib('maxlength',9);
        $hijos->setAttrib('class','v_numeric');
        $hijos->addFilter('StripTags');
        $this->addElement($hijos);
        
        $nacimiento = new Zend_Form_Element_Text('nacimiento');
        $nacimiento->setLabel('Nacimiento:');
        $nacimiento->addValidator(new Zend_Validate_Date('DD-MM-YYYY'));
        $nacimiento->setAttrib('maxlength',10);
        $nacimiento->setAttrib('class','v_datepicker');
        $nacimiento->addFilter('StripTags');
        $this->addElement($nacimiento);
        
        $dni = new Zend_Form_Element_Text('dni');
        $dni->setLabel('Dni:');
        $dni->setAttrib('maxlength',10);
        $dni->addFilter('StripTags');
        $this->addElement($dni);
    }

    public function populate($data)
    {
        $data['fecha'] = new Zend_Date($data['fecha'],'yyyy-mm-dd');
        $data['fecha'] = $data['fecha']->get('dd/mm/yyyy');
        $data['nacimiento'] = new Zend_Date($data['nacimiento'],'yyyy-mm-dd');
        $data['nacimiento'] = $data['nacimiento']->get('dd/mm/yyyy');
        return $this->setDefaults($data);
    }


}

