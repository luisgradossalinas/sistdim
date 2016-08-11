<?php

/**
 * Description of Edad
 *
 * @author dpozo
 */
class App_View_Helper_ItemList extends Zend_View_Helper_Abstract
{
    protected $_validClasses;
    
    public function __construct()
    {
        $this->_validClasses = array (
            'Idioma',
            'ProgramaComputo',
            'MedioPago',
            'Mes',
            'EstadoCompra',
            'DominioIdioma',
            'DominioProgramaComputo',
            'MedioPublicacion'
        );
    }
    
    public function ItemList($list, $key)
    {
        if ($key == 'combo') {
            $key = Application_Model_Tarifa::MEDIOPUB_APTITUS_TALAN;
        } 
        
        if (in_array(ucfirst($list), $this->_validClasses)) {
            $class = 'Application_Model_'.ucfirst($list);
            if (class_exists($class)) {
                $obj = new $class;
                $data = $obj->get();
                if (array_key_exists($key, $data))
                    return $data[$key];
                else
                    return '';
            } else {
                throw new Zend_Exception('No existe esta Clase: '.$class);
            }
        } else {
            throw new Zend_Exception('No existe esta Enumeración: '.$list);
        }
        
    }
    
}