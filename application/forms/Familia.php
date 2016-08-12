<?php

class Application_Form_Familia extends Zend_Form
{

    private $_grupo;
    
    public function init()
    {
        $this->setAttrib('id', 'form');
        
        $descripcion = new Zend_Form_Element_Text('descripcion');
        $descripcion->setLabel('Descripcion:');
        $descripcion->setRequired();
        $descripcion->setAttrib('maxlength',400);
        $descripcion->addFilter('StripTags');
        $this->addElement($descripcion);
        
        $estado = new Zend_Form_Element_Select('estado');
        $estado->setLabel('Estado:');
        $estado->setRequired();
        $estado->setMultiOptions(array('1' => 'Activo', '0' => 'Inactivo'));
        $estado->addFilter('StripTags');
        $this->addElement($estado);
        
        $this->_grupo = new Application_Model_Grupo;        
        $dataGrupo = $this->_grupo->combo();
        array_unshift($dataGrupo,array('key'=> '', 'value' => 'Seleccione'));
        
        $codigo_grupo = new Zend_Form_Element_Select('codigo_grupo');
        $codigo_grupo->setLabel('Grupo:');
        $codigo_grupo->setRequired();
        $codigo_grupo->setMultiOptions($dataGrupo);
        $this->addElement($codigo_grupo);

    }

    public function populate($data)
    {
        return $this->setDefaults($data);
    }


}

