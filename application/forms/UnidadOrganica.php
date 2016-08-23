<?php

class Application_Form_UnidadOrganica extends Zend_Form
{
    private $_organo;
    
    public function init()
    {
        $this->_organo = new Application_Model_Organo;
        
        $this->setAttrib('id', 'form');
        
        $dataOrgano = $this->_organo->combo();
        array_unshift($dataOrgano,array('key'=> '', 'value' => 'Seleccione'));
        
        $naturaleza = new Zend_Form_Element_Select('id_organo');
        $naturaleza->setLabel('Órgano:');
        $naturaleza->setRequired();
        $naturaleza->addFilter('StripTags');
        $naturaleza->setMultiOptions($dataOrgano);
        $this->addElement($naturaleza);
        
        $organo = new Zend_Form_Element_Text('descripcion');
        $organo->setLabel('Unidad Orgánica:');
        $organo->setRequired();
        $organo->addFilter('StripTags');
        $this->addElement($organo);

        $estado = new Zend_Form_Element_Select('estado');
        $estado->setLabel('Estado:');
        $estado->setRequired();
        $estado->setMultiOptions(array('1' => 'Activo', '0' => 'Inactivo'));
        $estado->addFilter('StripTags');
        $this->addElement($estado);
   
    }

}

