<?php

class Admin_GeneratorController extends App_Controller_Action_Admin
{
    
    const INACTIVO = 0;
    const ACTIVO = 1;
    const ELIMINADO = 2;
    
    public function init()
    {
        parent::init();
        $this->_form = new Application_Form_Generator;
        $this->_helper->layout->setLayout('generator');
        
    }
    
    public function indexAction()
    {      
        $this->view->headScript()->appendFile(SITE_URL.'/js/generator/config.js');
        
        $this->view->active = 'Generator de CRUD';
        Zend_Layout::getMvcInstance()->assign('link', 'generator');
        Zend_Layout::getMvcInstance()->assign('active', 'generator');
        Zend_Layout::getMvcInstance()->assign('padre', '5');
        
        $this->view->form = $this->_form;        
      
    }
    
    public function generarCodeAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
       
        if (!$this->getRequest()->isXmlHttpRequest())
            exit('Acción solo válida para peticiones ajax');
        
        $tabla = $this->_getParam('tabla');
        $formulario = ucfirst($tabla);
        $modelo = ucfirst($tabla);
        
        $generator = new Generator_Form();
        $modeloGenerator = new Generator_Modelo();
        
        $formMetodo = new Zend_CodeGenerator_Php_Method();
        $formMetodo->setName('init')
               ->setBody(
                    $generator->cuerpoFormulario($tabla)
                );
        
        $formClase = new Zend_CodeGenerator_Php_Class();
        $formClase->setName('Application_Form_'.$formulario.' extends Zend_Form')
              ->setMethod($formMetodo)
               ->setMethod(
                    array(
                        'name'       => 'populate',
                        'parameters' => array(
                            array('name' => 'data'),
                        ),
                        'body'       => $generator->populate($tabla)
                    )
               );
        
        $modeloClase = new Zend_CodeGenerator_Php_Class();
        $modeloClase->setName('Application_Model_'.$modelo.' extends Zend_Db_Table')
              ->setMethod(
                    array(
                        'name'       => 'guardar',
                        'parameters' => array(
                            array('name' => 'datos'),
                        ),
                        'body'       => $modeloGenerator->cuerpoModelo($tabla)
                    )
               )
               ->setMethod(
                    array(
                        'name'       => 'listado',
                        'body'       => 'return $this->getAdapter()->select()->from($this->_name)->query()->fetchAll();'
                    )
               );
        
        $modeloClase->setProperties(array(
            array(
                'name'         => '_name',
                'visibility'   => 'protected',
                'defaultValue' => $tabla,
            ),
            array(
                'name'         => '_primary',
                'visibility'   => 'protected',
                'defaultValue' => $modeloGenerator->getPrimaryKey($tabla),
            ),
            array(
                'name'         => 'ESTADO_INACTIVO',
                'const'        => true,
                'defaultValue' => 0,
            ),
            array(
                'name'         => 'ESTADO_ACTIVO',
                'const'        => true,
                'defaultValue' => 1,
            ),
            array(
                'name'         => 'ESTADO_ELIMINADO',
                'const'        => true,
                'defaultValue' => 2,
            ),
            array(
                'name'         => 'TABLA',
                'const'        => true,
                'defaultValue' => $tabla,
            ),
        ));
        
        $formArchivo = new Zend_CodeGenerator_Php_File();
        $formArchivo->setClass($formClase);
        
        $modeloArchivo = new Zend_CodeGenerator_Php_File();
        $modeloArchivo->setClass($modeloClase);
        
        //Donde guardar los archivos
        $rutaFormulario = APPLICATION_PATH.'/forms/'.$formulario.'.php';
        file_put_contents($rutaFormulario, $formArchivo->generate());
        @chmod($rutaFormulario, 0777);
        
        $rutaModelo = APPLICATION_PATH.'/models/'.$modelo.'.php';
        file_put_contents($rutaModelo, $modeloArchivo->generate());
        @chmod($rutaModelo, 0777);
        
        $rutaVista = APPLICATION_PATH.'/modules/admin/views/scripts/mvc/'.$tabla.'.phtml';
        $rutaGenerator = APPLICATION_PATH.'/modules/admin/views/scripts/mvc/generator.phtml';
        
        $vista = file_get_contents($rutaGenerator);
        $vistaSalida = str_replace('primaryKey', $modeloGenerator->getPrimaryKey($tabla), $vista);
        $vistaSalida = str_replace('$columnasBD', $modeloGenerator->getColumnas($tabla), $vistaSalida);
        $vistaSalida = str_replace('$datosBD', $modeloGenerator->getDatosBD($tabla), $vistaSalida);
        
        file_put_contents($rutaVista, $vistaSalida);
        @chmod($rutaVista, 0777);
        
        echo "Formulario Application_Form_".$formulario." generado correctamente.<br>";
        echo "Modelo Application_Model_".$modelo." generado correctamente.<br>";
        echo "Vista ".$tabla.".phtml generada correctamente.";
    }
 

}



