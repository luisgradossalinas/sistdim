<?php

class Application_Form_Organigrama extends Zend_Form
{
    private $_natuorganica;
    
    public function init()
    {
        $this->_natuorganica = new Application_Model_Natuorganica;
        
        $this->setAttrib('id', 'form');
        
        $dataNaturaleza = $this->_natuorganica->combo();
        array_unshift($dataNaturaleza,array('key'=> '', 'value' => 'Seleccione'));
        
        $naturaleza = new Zend_Form_Element_Select('codigo_natuorganica');
        $naturaleza->setLabel('Naturaleza:');
        $naturaleza->setRequired();
        $naturaleza->addFilter('StripTags');
        $naturaleza->setMultiOptions($dataNaturaleza);
        $this->addElement($naturaleza);
        
        $organo = new Zend_Form_Element_Text('organo');
        $organo->setLabel('Órgano:');
        $organo->setRequired();
        $organo->addFilter('StripTags');
        $this->addElement($organo);
        
        $unidadOrganica = new Zend_Form_Element_Text('unidad_organica');
        $unidadOrganica->setLabel('Unidad Orgánica:');
        $unidadOrganica->setRequired();
        $unidadOrganica->addFilter('StripTags');
        $this->addElement($unidadOrganica);
        
        
        $estado = new Zend_Form_Element_Select('estado');
        $estado->setLabel('Estado:');
        $estado->setRequired();
        $estado->setMultiOptions(array('1' => 'Activo', '0' => 'Inactivo'));
        $estado->addFilter('StripTags');
        $this->addElement($estado);
        
   
    }

}

