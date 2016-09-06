<?php

class Application_Form_Proyecto extends Zend_Form
{

    private $_entidadModel;

    public function init()
    {
        $this->setAttrib('id', 'form');
        
        $nombre = new Zend_Form_Element_Text('nombre');
        $nombre->setLabel('Proyecto:');
        $nombre->setAttrib('maxlength',200);
        $nombre->addFilter('StripTags');
        $this->addElement($nombre);
        
        $inicio = new Zend_Form_Element_Text('inicio');
        $inicio->setLabel('Inicio:');
        $inicio->addValidator(new Zend_Validate_Date('DD-MM-YYYY'));
        $inicio->setAttrib('maxlength',10);
        $inicio->setAttrib('class','v_datepicker');
        $inicio->addFilter('StripTags');
        $this->addElement($inicio);
        
        $fin = new Zend_Form_Element_Text('fin');
        $fin->setLabel('Fin:');
        $fin->addValidator(new Zend_Validate_Date('DD-MM-YYYY'));
        $fin->setAttrib('maxlength',10);
        $fin->setAttrib('class','v_datepicker');
        $fin->addFilter('StripTags');
        $this->addElement($fin);
        
        $tieneMapaPuesto = new Zend_Form_Element_Select('mapa_puesto');
        $tieneMapaPuesto->setLabel('¿Tiene mapeo de puesto?');
        $tieneMapaPuesto->setRequired();
        $tieneMapaPuesto->setMultiOptions(array('1' => 'No', '0' => 'Sí'));
        $this->addElement($tieneMapaPuesto);
        
        $estado = new Zend_Form_Element_Select('estado');
        $estado->setLabel('Estado:');
        $estado->setRequired();
        $estado->setMultiOptions(array('1' => 'Activo', '0' => 'Inactivo'));
        $estado->addFilter('StripTags');
        $this->addElement($estado);


        $this->_entidadModel = new Application_Model_Entidad;
        $dataEntidad = $this->_entidadModel->combo();
        array_unshift($dataEntidad,array('key'=> '', 'value' => 'Seleccione'));

        $id_entidad = new Zend_Form_Element_Select('id_entidad');
        $id_entidad->setLabel('Entidad:');
        $id_entidad->setRequired();
        $id_entidad->setMultiOptions($dataEntidad);
        $this->addElement($id_entidad);


    }

    public function populate($data)
    {
        if (isset($data['inicio']) && ($data['inicio'] == App_View_Helper_FechaMostrar::DEFAULT_DATE || $data['inicio'] == App_View_Helper_FechaMostrar::DEFAULT_DATETIME)) {
            unset($data['inicio']);
        } else {
            $data['inicio'] = new Zend_Date($data['inicio'],'yyyy-mm-dd');
            $data['inicio'] = $data['inicio']->get('dd/mm/yyyy');
            } 
            if (isset($data['fin']) && ($data['fin'] == App_View_Helper_FechaMostrar::DEFAULT_DATE || $data['fin'] == App_View_Helper_FechaMostrar::DEFAULT_DATETIME)) {
            unset($data['fin']);
        } else {
            $data['fin'] = new Zend_Date($data['fin'],'yyyy-mm-dd');
            $data['fin'] = $data['fin']->get('dd/mm/yyyy');
            } 
        return $this->setDefaults($data);
    }


}

