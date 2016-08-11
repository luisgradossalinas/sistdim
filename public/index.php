<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
    /*
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));*/

ini_set('include_path', realpath(dirname(__FILE__).'/../library/'));

/** Zend_Application */
require_once 'Zend/Application.php';

require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance ();

$translator = new Zend_Translate(
                'array',
                '../resources/languages',
                'es',
                array('scan' => Zend_Translate::LOCALE_DIRECTORY)
);
Zend_Validate_Abstract::setDefaultTranslator($translator);

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);


$application->bootstrap()
            ->run();