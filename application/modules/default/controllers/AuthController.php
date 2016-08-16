<?php

class Default_AuthController extends Zend_Controller_Action {

    private $_formLogin;
    private $_usuarioModel;
    private $_proyectoModel;

    public function init() {
        $this->_formLogin = new Application_Form_Login;
        $this->_usuarioModel = new Application_Model_Usuario;
        $this->_proyectoModel = new Application_Model_Proyecto;

        $this->_helper->layout->setLayout('login');
    }

    public function indexAction() {
        
    }

    public function loginAction() {
        
        $this->view->messages = "";
        $this->view->msg = "";

        //Si está logueado y vuelve atràs o por url lo envia al admin
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $this->_redirect('/admin');
        }

        if ($this->getRequest()->isPost()) {

            $data = $this->_getAllParams();
            $f = new Zend_Filter_StripTags();

            //Si existe la variable emailrecover buscar si existe email
            if ($this->_hasParam('emailrecover')) {
                $correo = $f->filter($data['emailrecover']);

                $dataCorreo = $this->_usuarioModel->existeUsuarioEmail($correo);

                if (empty($dataCorreo)) {
                    $this->view->messages = 'No existe usuario con ese email';
                    $this->view->msg = 'alert-error';
                } else {
                    //Enviar correo para reestablecer contraseña
                    //Validar si email existe
                    //Establecer token
                    $hash = new Zend_Form_Element_Hash('csrf_hash', array('salt' => 'exitsalt'));
                    $hash->setTimeout(120); // 2min
                    $hash->initCsrfToken();
                    $token = $hash->getValue();

                    $this->_usuarioModel->generarToken($correo, $token);
                    //Creamos email
                    $mail = new Zend_Mail();
                    $mail->addTo($correo);
                    $mail->setSubject('Cambio de clave');
                    //$mail->setBodyHtml($html)
                    $mail->setBodyHtml('Para poder cambiar su clave debe ingresar al siguiente link:<br>'
                            . SITE_URL.'/recuperar-clave/token/'.$token." <br>");
                    $sent = true;

                    try {
                        $mail->send();
                        $this->view->messages = 'Correo enviado satisfactoriamente';
                        $this->view->msg = 'alert-success';
                    } catch (Exception $e) {
                        $sent = false;
                        $this->view->messages = 'No se pudo enviar el correo.';
                        $this->view->msg = 'alert-error';
                        //print_r($e);
                    }
                }
            }

            if ($this->_hasParam('usuario')) {

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

                    $dataValida = $this->_validaAccesoProyecto($data);
                    if ($dataValida['code'] == 0) {
                        Zend_Auth::getInstance()->clearIdentity();
                        $this->view->messages = $dataValida['msg'];
                        $this->view->msg = 'alert-error';
                    } else {
                        //Si tiene proyectos
                        if ($dataValida['code'] == 1) {
                            $this->_guardarSesion($data);
                            $this->_redirect('lista-proyectos');
                        }

                        //Si es administrador
                        $this->_guardarSesion($data);
                        $this->_redirect('admin');
                    }
                } else {
                    //$this->_redirect('login');
                    $this->view->messages = 'Usuario o clave incorrectos.';
                    $this->view->msg = 'alert-error';
                    //return;
                }
            }
        }
    }

    public function logoutAction() {
        Zend_Auth::getInstance()->clearIdentity();
        //Redirect de acuerdo al módulo
        $this->_redirect('login');
    }

    /**
     * Guarda el username en la sesión
     * @param String $username 
     */
    private function _guardarSesion($username) {

        settype($username, 'array');
        $sesion_usuario = new Zend_Session_Namespace('sesion_usuario');
        $rolModelo = new Application_Model_Rol;

        $dataRol = $rolModelo->fetchRow("id = " . $username['id_rol'])->toArray();
        $usuario = $username;
        $usuario['nombre_rol'] = $dataRol['nombre'];
        $usuario['id_proyecto'] = '';
        $usuario['nombre_proyecto'] = '';
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

    //Si es admin no validarlo (retornar mensaje)
    public function _validaAccesoProyecto($data) {

        $rolRecursoModel = new Application_Model_RolRecurso;
        $usuario = $data->id;
        $rol = $data->id_rol;

        if ($rol != Application_Model_Rol::ADMINISTRADOR) {
            $validaAcceso = $rolRecursoModel->validarUsuario($usuario);
            if (empty($validaAcceso)) {
                return array('code' => 0, 'msg' => 'No tiene proyectos asignados');
            } else {
                return array('code' => 1, 'msg' => 'Redireccionar a lista proyectos');
            }
        }

        return array('code' => 2, 'msg' => 'Acceso administrador');
    }

    public function listaProyectosAction() {

        $sesion_usuario = new Zend_Session_Namespace('sesion_usuario');
        $usuario = $sesion_usuario->sesion_usuario['id'];
        $this->view->proyectos = $this->_proyectoModel->proyectosActivosUsuario($usuario);

        if ($this->getRequest()->isPost()) {

            //setear sesión 
            $data = $this->_getAllParams();
            $f = new Zend_Filter_StripTags();
            $proyecto = $f->filter($data['proyecto']);
            $sesion_usuario = new Zend_Session_Namespace('sesion_usuario');
            $proyectoModelo = new Application_Model_Proyecto;
            $dataProyecto = $proyectoModelo->fetchRow("id_proyecto = " . $proyecto)->toArray();
            $sesion_usuario->sesion_usuario['id_proyecto'] = $proyecto;
            $sesion_usuario->sesion_usuario['nombre_proyecto'] = $dataProyecto['nombre'];

            $this->_redirect('admin');
        }
    }

    /*
      Cambiar contraseña de usuario
     */

    public function recuperarClaveAction() {

        if (!$this->_getParam('token')) {
            $this->_redirect('login');
        }

        $token = $this->_getParam('token');
        $dataToken = $this->_usuarioModel->existeToken($token);

        if (empty($dataToken)) {
            $this->_redirect('login');
        }

        $correo = $dataToken[0]['email'];
        $this->view->correo = $correo;
        $this->view->token = $token;

        if ($this->getRequest()->isPost()) {
            $data = $this->_getAllParams();
            $f = new Zend_Filter_StripTags();
            $clave = $f->filter($data['clave']);

            $this->_usuarioModel->cambiarClave($correo, $clave, $token);
            $this->view->messages = 'Clave cambiada satisfactoriamente.';
            $this->view->msg = 'alert-success';
        }
    }

}
