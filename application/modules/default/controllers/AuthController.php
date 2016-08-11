<?php

class Default_AuthController extends Zend_Controller_Action
{

    private $_formLogin;
    private $_usuarioModel;
    
    public function init()
    {
        $this->_formLogin = new Application_Form_Login;
        $this->_usuarioModel = new Application_Model_Usuario;
        
        $this->_helper->layout->setLayout('login');
    }
    
    public function indexAction()
    {
        
    }
    
    public function loginAction()
    {
        $this->view->messages = "";
        
        //Si está logueado y vuelve atràs o por url lo envia al admin
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $this->_redirect('/admin');
        }
     
        if ($this->getRequest()->isPost()) {
            
            $data = $this->_getAllParams();
            $f = new Zend_Filter_StripTags();

                $username = $f->filter($data['usuario']);
                $password = $f->filter($data['clave']);

                Zend_Loader::loadClass('Zend_Auth_Adapter_DbTable');
                $dbAdapter = $this->_usuarioModel->getAdapter();

                $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
                $authAdapter->setTableName('usuario');
                $authAdapter->setIdentityColumn('email');
                $authAdapter->setCredentialColumn('clave');

                $authAdapter->setIdentity($username);
                $authAdapter->setCredential(md5($password));
                //$auth = Zend_Auth::getInstance();
                $result = $auth->authenticate($authAdapter);

                if ($result->isValid()) {

                    $data = $authAdapter->getResultRowObject(null, 'password');
                    $auth->getStorage()->write($data);

                    $this->_guardarSesion($data);
                    $this->_redirect('admin');
                } else {
                    //$this->_redirect('login');
                    $this->view->messages = 'Usuario o clave incorrectos.';
                    //return;
                }
        }
        //$this->render('login');
        
      //  $this->view->form = $this->_formLogin;
    }
    
    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        //Redirect de acuerdo al módulo
        $this->_redirect('login');
               
    }
    
    /**
     * Guarda el username en la sesión
     * @param String $username 
     */
    private function _guardarSesion($username) {
        
        settype($username,'array');
        $sesion_usuario = new Zend_Session_Namespace('sesion_usuario');
        $rolModelo = new Application_Model_Rol;
        $dataRol = $rolModelo->fetchRow("id = " . $username['id_rol'])->toArray();
        $usuario = $username;
        $usuario['nombre_rol'] = $dataRol['nombre'];
        $usuario['nombre_completo'] = $username['nombres'] . " " . $username['apellidos'];
        $sesion_usuario->sesion_usuario = $usuario;
        
    }
    
    /**
     * Verifica si el usuario ya está logueado
     */
    public function _logueado() {
        
        $login = Zend_Auth::getInstance();
        if ($login->hasIdentity()) {
            $this->_redirect("admin");
        }
        
    }

}

