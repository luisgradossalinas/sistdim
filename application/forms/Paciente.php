<?php

class Application_Form_Paciente extends Zend_Form
{

    public function init()
    {
        $this->setAttrib('id', 'form');
        
        $nombre = new Zend_Form_Element_Text('nombre');
        $nombre->setLabel('Nombre:');
        $nombre->setRequired();
        $nombre->setAttrib('maxlength',100);
        $nombre->addFilter('StripTags');
        $this->addElement($nombre);
        
        $apellido_paterno = new Zend_Form_Element_Text('apellido_paterno');
        $apellido_paterno->setLabel('Apellido_paterno:');
        $apellido_paterno->setRequired();
        $apellido_paterno->setAttrib('maxlength',100);
        $apellido_paterno->addFilter('StripTags');
        $this->addElement($apellido_paterno);
        
        $apellido_materno = new Zend_Form_Element_Text('apellido_materno');
        $apellido_materno->setLabel('Apellido_materno:');
        $apellido_materno->setRequired();
        $apellido_materno->setAttrib('maxlength',100);
        $apellido_materno->addFilter('StripTags');
        $this->addElement($apellido_materno);
        
        $sexo = new Zend_Form_Element_Text('sexo');
        $sexo->setLabel('Sexo:');
        $sexo->addValidator(new Zend_Validate_Int());
        $sexo->setAttrib('maxlength',3);
        $sexo->setAttrib('size',5);
        $sexo->setAttrib('class','v_numeric');
        $sexo->addFilter('StripTags');
        $this->addElement($sexo);
        
        $tipo_doc = new Zend_Form_Element_Text('tipo_doc');
        $tipo_doc->setLabel('Tipo_doc:');
        $tipo_doc->setAttrib('maxlength',50);
        $tipo_doc->addFilter('StripTags');
        $this->addElement($tipo_doc);
        
        $num_doc = new Zend_Form_Element_Text('num_doc');
        $num_doc->setLabel('Num_doc:');
        $num_doc->setRequired();
        $num_doc->setAttrib('maxlength',20);
        $num_doc->addFilter('StripTags');
        $this->addElement($num_doc);
        
        $telefono = new Zend_Form_Element_Text('telefono');
        $telefono->setLabel('Telefono:');
        $telefono->setAttrib('maxlength',20);
        $telefono->addFilter('StripTags');
        $this->addElement($telefono);
        
        $celular = new Zend_Form_Element_Text('celular');
        $celular->setLabel('Celular:');
        $celular->setRequired();
        $celular->setAttrib('maxlength',20);
        $celular->addFilter('StripTags');
        $this->addElement($celular);
        
        $sueldo = new Zend_Form_Element_Text('sueldo');
        $sueldo->setLabel('Sueldo:');
        $sueldo->addFilter('StripTags');
        $this->addElement($sueldo);
        
        $direccion = new Zend_Form_Element_Text('direccion');
        $direccion->setLabel('Direccion:');
        $direccion->setAttrib('maxlength',100);
        $direccion->addFilter('StripTags');
        $this->addElement($direccion);
        
        $fecha_nacimiento = new Zend_Form_Element_Text('fecha_nacimiento');
        $fecha_nacimiento->setLabel('Fecha_nacimiento:');
        $fecha_nacimiento->addValidator(new Zend_Validate_Date('DD-MM-YYYY'));
        $fecha_nacimiento->setAttrib('maxlength',10);
        $fecha_nacimiento->setAttrib('class','v_datepicker');
        $fecha_nacimiento->addFilter('StripTags');
        $this->addElement($fecha_nacimiento);
        
        $fh_registro = new Zend_Form_Element_Text('fh_registro');
        $fh_registro->setLabel('Fh_registro:');
        $fh_registro->addValidator(new Zend_Validate_Date('DD-MM-YYYY'));
        $fh_registro->setAttrib('maxlength',10);
        $fh_registro->setAttrib('class','v_datepicker');
        $fh_registro->addFilter('StripTags');
        $this->addElement($fh_registro);
    }

    public function populate($data)
    {
        if (isset($data['fecha_nacimiento']) && ($data['fecha_nacimiento'] == App_View_Helper_FechaMostrar::DEFAULT_DATE || $data['fecha_nacimiento'] == App_View_Helper_FechaMostrar::DEFAULT_DATETIME)) {
            unset($data['fecha_nacimiento']);
        } else {
            $data['fecha_nacimiento'] = new Zend_Date($data['fecha_nacimiento'],'yyyy-mm-dd');
            $data['fecha_nacimiento'] = $data['fecha_nacimiento']->get('dd/mm/yyyy');
            } 
            if (isset($data['fh_registro']) && ($data['fh_registro'] == App_View_Helper_FechaMostrar::DEFAULT_DATE || $data['fh_registro'] == App_View_Helper_FechaMostrar::DEFAULT_DATETIME)) {
            unset($data['fh_registro']);
        } else {
            $data['fh_registro'] = new Zend_Date($data['fh_registro'],'yyyy-mm-dd');
            $data['fh_registro'] = $data['fh_registro']->get('dd/mm/yyyy');
            } 
        return $this->setDefaults($data);
    }


}

