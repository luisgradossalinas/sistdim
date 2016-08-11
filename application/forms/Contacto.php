<?php

class Application_Form_Contacto extends Zend_Form
{

    public function init()
    {
        $this->setAttrib('id', 'form');
        
        $nombre = new Zend_Form_Element_Text('nombres');
        $nombre->setLabel('Nombre:');
        $nombre->setRequired();
        $nombre->addFilter('StripTags');
        $nombre->setAttrib('maxlength', 50);
        $nombre->addValidator(new Zend_Validate_StringLength(array('min' => 3)));
        $this->addElement($nombre);
        
        $celular = new Zend_Form_Element_Text('celular');
        $celular->setLabel('Celular:');
        $celular->setRequired();
        $celular->setAttrib('class', 'v_numeric');
        $celular->addFilter('StripTags');
        $celular->addValidator(new Zend_Validate_Int());
        $this->addElement($celular);
        
        $correo = new Zend_Form_Element_Text('correo');
        $correo->setLabel('Correo:');
        $correo->setRequired();
        $correo->addValidator(new Zend_Validate_EmailAddress());
        $correo->addFilter('StripTags');
        $this->addElement($correo);
        
        $mensaje = new Zend_Form_Element_Textarea('mensaje');
        $mensaje->setLabel('Mensaje:');
        $mensaje->setRequired();
        $mensaje->setAttribs(array('cols' => 20, 'rows' => 5));
        $mensaje->addFilter('StripTags');
        $this->addElement($mensaje);
        
        $respondido = new Zend_Form_Element_Checkbox('respondido');
        $respondido->setLabel('Respondido:');
        $respondido->setRequired();
        $respondido->addFilter('StripTags');
        $this->addElement($respondido);
        
   
    }


}

