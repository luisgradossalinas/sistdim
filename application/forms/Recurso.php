<?php

class Application_Form_Recurso extends Zend_Form
{
    private $_recurso;
    
    public function init()
    {
        $this->_recurso = new Application_Model_Recurso;
        
        $this->setAttrib('id', 'form');
        
        $dataRecurso = $this->_recurso->listaRecursosPadre();
        array_unshift($dataRecurso,array('key'=> '', 'value' => 'Seleccione'));
        
        $nombre = new Zend_Form_Element_Text('nombre');
        $nombre->setLabel('Nombre:');
        $nombre->setRequired();
        $nombre->addFilter('StripTags');
        $nombre->setAttrib('maxlength', 50);
        $nombre->addValidator(new Zend_Validate_StringLength(array('min' => 3)));
        $nombre->addValidator('Alpha', false, array('allowWhiteSpace' => true));
        $this->addElement($nombre);
        
        $access = new Zend_Form_Element_Text('access');
        $access->setLabel('Access:');
        $access->setRequired();
        $access->addFilter('StripTags');
        $access->setAttrib('maxlength', 50);
        $access->addValidator(new Zend_Validate_StringLength(array('min' => 4)));
        $this->addElement($access);
        
        $descripcion = new Zend_Form_Element_Text('accion');
        $descripcion->setLabel('Descripción:');
        $descripcion->setRequired();
        $descripcion->addFilter('StripTags');
        $this->addElement($descripcion);
        
        $padre = new Zend_Form_Element_Select('padre');
        $padre->setLabel('Padre:');
        //$padre->setRequired();
        
        $padre->addFilter('StripTags');
        $padre->setMultiOptions($dataRecurso);
        $this->addElement($padre);
        
        $orden = new Zend_Form_Element_Text('orden');
        $orden->setLabel('Orden:');
        $orden->setRequired();
        $orden->setAttrib('class','v_numeric');
        $orden->setAttrib('readonly','readonly');        
        $orden->addFilter('StripTags');
        $this->addElement($orden);
        
        $url = new Zend_Form_Element_Text('url');
        $url->setLabel('Url:');
        $url->addFilter('StripTags');
        $this->addElement($url);
        
        $funcionListado = new Zend_Form_Element_Select('funcion_listado');
        $funcionListado->setLabel('Función para listar:');
        //$funcionListado->setValue('fetchAll');
        $dataFN = array();
        array_unshift($dataFN,array('key'=> 'listado', 'value' => 'listado'));
        array_unshift($dataFN,array('key'=> 'fetchAll', 'value' => 'fetchAll'));
        $funcionListado->setMultiOptions($dataFN);
        $funcionListado->addFilter('StripTags');
        $this->addElement($funcionListado);
        
        /*
        $tab = new Zend_Form_Element_Text('tab');
        $tab->setLabel('Tab:');
        $tab->addFilter('StripTags');
        $this->addElement($tab);
        */
        
        $estado = new Zend_Form_Element_Select('estado');
        $estado->setLabel('Estado:');
        $estado->setRequired();
        $estado->setMultiOptions(array('1' => 'Activo', '0' => 'Inactivo'));
        $estado->addFilter('StripTags');
        $this->addElement($estado);
   
    }

}

