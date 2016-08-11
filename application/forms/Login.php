<?php

class Application_Form_Login extends Zend_Form
{

    public function init()
    {
        $this->setAttrib('id', 'form-login');
        
        $usuario = new Zend_Form_Element_Text('usuario');
        $usuario->setLabel('Usuario:');
        $usuario->setRequired();
        $usuario->addFilter('StripTags');
        $usuario->setAttrib('maxlength', 50);
        $usuario->addValidator(new Zend_Validate_StringLength(array('min' => 4)));
        $usuario->addValidator('Alpha', false, array('allowWhiteSpace' => true));
        $this->addElement($usuario);
        
        $clave = new Zend_Form_Element_Text('clave');
        $clave->setLabel('Contraseña:');
        $clave->setRequired();
        $clave->addFilter('StripTags');
        $this->addElement($clave);
   
    }


}

