<?php

class Application_Form_Distrito extends Zend_Form
{

    public function init()
    {
        $this->setAttrib('id', 'form');
        
        $nombre = new Zend_Form_Element_Text('nombre');
        $nombre->setLabel('Nombre:');
        $nombre->setRequired();
        $nombre->addFilter('StripTags');
        $nombre->setAttrib('maxlength', 50);
        $nombre->addValidator(new Zend_Validate_StringLength(array('min' => 4)));
        $nombre->addValidator('Alpha', false, array('allowWhiteSpace' => true));
        $this->addElement($nombre);
  
   
    }


}

