<?php

class Application_Form_Generator extends Zend_Form
{

    private $_generator;
    
    public function init()
    {
        $this->_generator = new Application_Model_Generator();
        $this->setAttrib('id', 'form');
        
        $dataTabla = $this->_generator->listaTablas();
        array_unshift($dataTabla,array('key'=> 'T', 'value' => 'Todas las tablas'));
        array_unshift($dataTabla,array('key'=> '', 'value' => 'Seleccione'));
        
        
        $tabla = new Zend_Form_Element_Select('id_tabla');
        $tabla->setLabel('Tablas:');
        $tabla->setRequired();
        $tabla->setAttrib('style', 'display:""');
        $tabla->setMultiOptions($dataTabla);
        $this->addElement($tabla);
        
    }


}

