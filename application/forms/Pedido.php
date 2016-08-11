<?php

class Application_Form_Pedido extends Zend_Form
{

    public function init()
    {
        $this->setAttrib('id', 'form');
        
        $subtotal = new Zend_Form_Element_Text('subtotal');
        $subtotal->setLabel('Subtotal:');
        $subtotal->addFilter('StripTags');
        $this->addElement($subtotal);
        
        $igv = new Zend_Form_Element_Text('igv');
        $igv->setLabel('Igv:');
        $igv->addFilter('StripTags');
        $this->addElement($igv);
        
        $total = new Zend_Form_Element_Text('total');
        $total->setLabel('Total:');
        $total->addFilter('StripTags');
        $this->addElement($total);
        
        $estado = new Zend_Form_Element_Text('estado');
        $estado->setLabel('Estado:');
        $estado->setRequired();
        $estado->addValidator(new Zend_Validate_Int());
        $estado->setAttrib('maxlength',3);
        $estado->setAttrib('size',5);
        $estado->setAttrib('class','v_numeric');
        $estado->addFilter('StripTags');
        $this->addElement($estado);
        
        $entregado = new Zend_Form_Element_Text('entregado');
        $entregado->setLabel('Entregado:');
        $entregado->setRequired();
        $entregado->addValidator(new Zend_Validate_Int());
        $entregado->setAttrib('maxlength',3);
        $entregado->setAttrib('size',5);
        $entregado->setAttrib('class','v_numeric');
        $entregado->addFilter('StripTags');
        $this->addElement($entregado);
        
        $fecha_genera = new Zend_Form_Element_Text('fecha_genera');
        $fecha_genera->setLabel('Fecha_genera:');
        $fecha_genera->setRequired();
        $fecha_genera->addValidator(new Zend_Validate_Date());
        $fecha_genera->setAttrib('maxlength',10);
        $fecha_genera->setAttrib('class','v_datepicker');
        $fecha_genera->addFilter('StripTags');
        $this->addElement($fecha_genera);
        
        $fecha_entrega = new Zend_Form_Element_Text('fecha_entrega');
        $fecha_entrega->setLabel('Fecha_entrega:');
        $fecha_entrega->addValidator(new Zend_Validate_Date());
        $fecha_entrega->setAttrib('maxlength',10);
        $fecha_entrega->setAttrib('class','v_datepicker');
        $fecha_entrega->addFilter('StripTags');
        $this->addElement($fecha_entrega);
        
        $id_usuario = new Zend_Form_Element_Text('id_usuario');
        $id_usuario->setLabel('Id_usuario:');
        $id_usuario->addValidator(new Zend_Validate_Int());
        $id_usuario->setAttrib('maxlength',9);
        $id_usuario->setAttrib('class','v_numeric');
        $id_usuario->addFilter('StripTags');
        $this->addElement($id_usuario);
        
        $usuario_atendio = new Zend_Form_Element_Text('usuario_atendio');
        $usuario_atendio->setLabel('Usuario_atendio:');
        $usuario_atendio->addValidator(new Zend_Validate_Int());
        $usuario_atendio->setAttrib('maxlength',9);
        $usuario_atendio->setAttrib('class','v_numeric');
        $usuario_atendio->addFilter('StripTags');
        $this->addElement($usuario_atendio);
    }


}

