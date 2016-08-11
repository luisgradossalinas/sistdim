<?php

class Application_Form_Producto extends Zend_Form
{
    private $_categoriaModel;
    
    public function init()
    {
        $this->_categoriaModel = new Application_Model_Categoria;
        $this->setAttrib('id', 'form');
        
        $nombre = new Zend_Form_Element_Text('nom_prod');
        $nombre->setLabel('Producto:');
        $nombre->setRequired();
        $nombre->addFilter('StripTags');
        $this->addElement($nombre);
        
        $apellido = new Zend_Form_Element_Text('precio');
        $apellido->setLabel('Precio:');
        $apellido->setRequired();
        $apellido->addFilter('StripTags');
        $this->addElement($apellido);
        
        
        $dataCategoria = $this->_categoriaModel->combo();
        array_unshift($dataCategoria,array('key'=> '', 'value' => 'Seleccione'));
        
        $categoria = new Zend_Form_Element_Select('id_categoria');
        $categoria->setLabel('Categoría:');
        $categoria->setRequired();
        $categoria->setMultiOptions($dataCategoria);
        $this->addElement($categoria);
        
        $imagen = new Zend_Form_Element_File('imagen');
        $imagen->setLabel('Imagen:');
        $imagen->setRequired();
        $this->addElement($imagen);
        
        $estado = new Zend_Form_Element_Select('estado');
        $estado->setLabel('Estado:');
        $estado->setRequired();
        $estado->setMultiOptions(array('1' => 'Activo', '0' => 'Inactivo'));
        $estado->addFilter('StripTags');
        $this->addElement($estado);
        
    
    }


}

