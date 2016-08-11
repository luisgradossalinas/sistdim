<?php

class App_Controller_Action_Helper_Autocomplete 
    extends Zend_Controller_Action_Helper_Abstract
{
    private static $_allowedModels = array(
        'institucion',
        'carrera'
    );
    private static $_methodName = 'autocomplete';
    
    public function direct($params)
    {
        if (strlen($params['q'])<1) {
            throw new Zend_Exception('El query debe tener al menos 1 caracter');
        }
        
        if (!array_key_exists('subset', $params)) {
            $params['subset'] = null;
        }
        //var_dump($params);exit;
        $model = $params['model'];
        
        if (!in_array($model, self::$_allowedModels)) {
            throw new Zend_Exception('Modelo no permitido para auto-complete');
        }
        
        $modelName = 'Application_Model_'.ucfirst($model);
        if (!class_exists($modelName)) {
            throw new Zend_Exception('Clase no existe');
        }
        
        $model = new $modelName();
        
        if (!is_callable(array($model,self::$_methodName))) {
            throw new Zend_Exception('No existe el método '.self::$_methodName);
        }
        
        $res = $model->{self::$_methodName}($params['q'], $params['subset'], $params['nivel']);
        
        return $this->decorator($res, $params['q']);
    }
    
    private function decorator($res,$q)
    {
        array_walk(
            $res, 
            function(&$value, $key, $q) {
            $value = str_replace($q, '<b>'.$q.'</b>', $value);
            }, $q
        );
        $jsonBuggy = Zend_Json::encode($res);
        $json = str_replace("\\/", "/", $jsonBuggy);
        return $json;
    }

}