<?php

class Application_Form_Imagen extends Zend_Form
{

    public function init()
    {
        $this->setAttrib('enctype', 'multipart/form-data'); //Upload de archivos
        $this->setAttrib('id', 'form');
        
        $foto = new Zend_Form_Element_File('foto');
        $foto->setLabel('Foto:');
        $foto->setRequired();
        $foto->addValidator( 'Extension', false, 'jpg,png,gif,jpeg');
        $foto->addValidator( 'Size', false, '10024000' );
        $foto->setDestination( APPLICATION_PATH . '/../public/img/imagenes/' )
            ->setValueDisabled( true );
        $this->addElement($foto);
        
        $descripcion = new Zend_Form_Element_Text('descripcion');
        $descripcion->setLabel('Descripcion:');
        $descripcion->setAttrib('maxlength',100);
        $descripcion->addFilter('StripTags');
        $this->addElement($descripcion);
    }


}

