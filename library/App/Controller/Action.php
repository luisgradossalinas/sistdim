<?php

class App_Controller_Action extends Zend_Controller_Action
{
    
    //Constantes mensajes
    const WARNING = '';
    const SUCCESS = 'success';
    const ERROR = 'error';
    const INFO = 'info';
    
    const ACTUALIZAR = 'UPDATE';
    const NUEVO = 'NEW';
    
    const ACCESO_DENEGADO = 0;
    
    const MODULO_ADMIN = 'admin';

    public function init()
    {
        
    }
  
    public function getConfig()
    {
        return Zend_Registry::get('config');
    }

    public function getCache()
    {
        return Zend_Registry::get('cache');
    }

    public function getAdapter()
    {
        return Zend_Registry::get('db');
    }

    public function getLog()
    {
        return Zend_Registry::get('log');
    }

 }