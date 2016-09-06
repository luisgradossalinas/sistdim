<?php

class Application_Form_Rolpuesto extends Zend_Form
{

    private $_familia;

    public function init()
    {
        $this->setAttrib('id', 'form');
        
        $descripcion = new Zend_Form_Element_Text('descripcion');
        $descripcion->setLabel('Rol:');
        $descripcion->setRequired();
        $descripcion->setAttrib('maxlength',100);
        $descripcion->addFilter('StripTags');
        $this->addElement($descripcion);
        
        $estado = new Zend_Form_Element_Select('estado');
        $estado->setLabel('Estado:');
        $estado->setRequired();
        $estado->setMultiOptions(array('1' => 'Activo', '0' => 'Inactivo'));
        $estado->addFilter('StripTags');
        $this->addElement($estado);
        
        $this->_familia = new Application_Model_Familia;        
        $dataFamilia = $this->_familia->combo();
        array_unshift($dataFamilia,array('key'=> '', 'value' => 'Seleccione'));
        
        $codigo_familia = new Zend_Form_Element_Select('codigo_familia');
        $codigo_familia->setLabel('Familia:');
        $codigo_familia->setRequired();
        $codigo_familia->setMultiOptions($dataFamilia);
        $this->addElement($codigo_familia);

    }

    public function populate($data)
    {
        return $this->setDefaults($data);
    }


}

