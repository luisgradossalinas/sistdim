<?php

class App_Controller_Action_Helper_Mail extends Zend_Controller_Action_Helper_Abstract
{

    private $_mail;

    /*public function sendMailPerfilPostulante($data, $avisoUrl)
    {
        
        $bodyText = $data['nombreEmisor']
            . " te recomienda ver el Perfil: $avisoUrl \n\n"
            . $data['mensajeCompartir'];
        $this->_mail->setBodyText($bodyText);
        $this->_mail->addTo($data['correoReceptor']);
        $this->_mail->setSubject(
            "Estimado " . $data['nombreReceptor'] .
            " le han recomendado un puesto de trabajo"
        );
        $this->_mail->send();
    }*/
/*
    public function recomendarAviso($data, $avisoUrl)
    {
        $bodyText =
            "Tu amigo " . $data['nombreEmisor']
            . " te recomienda el puesto: $avisoUrl \n\n"
            . $data['mensajeCompartir'];
        $this->_mail->setBodyText($bodyText);
        $this->_mail->addTo($data['correoReceptor']);
        $this->_mail->setSubject(
            "Estimado " . $data['nombreReceptor'] .
            " le han recomendado un puesto de trabajo"
        );
        $this->_mail->send();
    }
*/
    /*
    public function sendMailNuevoAdmiUsuario($receiver, $data)
    {
        $subject = 'Clave Aptitus.';
        $bodyText =
            "Su contraseña es :"
            . $data;
        $this->_mail->setBodyText($bodyText);
        $this->_mail->addTo($receiver);
        $this->_mail->setSubject($subject);
        $this->_mail->send();
    }
*/
    public function __call($name, $arguments)
    {
        $this->_mail = new Zend_Mail('utf-8');
        $options = $arguments[0];
        $f = new Zend_Filter_Word_CamelCaseToDash();
        $tplDir = APPLICATION_PATH . '/../emailing/';
        $mailView = new Zend_View();
        $layoutView = new Zend_View();
        $mailView->setScriptPath($tplDir);
        $layoutView->setScriptPath($tplDir);
        $nombreTpl = strtolower($f->filter($name));
        $template = $nombreTpl . '.phtml';
        $subjects = new Zend_Config_Ini(APPLICATION_PATH . '/configs/mailing.ini', 'subjects');
        $subjects = $subjects->toArray();
        if (!is_readable(realpath($tplDir . $template))) {
            throw new Zend_Mail_Exception('No existe template para este email');
        }
        if (!array_key_exists($name, $subjects) || trim($subjects[$name]) == "") {
            throw new Zend_Mail_Exception('Subject no puede ser vacío, verificar mailing.ini');
        } else {
            $options['subject'] = $subjects[$name];
        }
        if (!array_key_exists('to', $options)) {
            throw new Zend_Mail_Exception('Falta indicar destinatario en $options["to"]');
        } else {
            $v = new Zend_Validate_EmailAddress();
            if (!$v->isValid($options['to'])) {
                //throw new Zend_Mail_Exception('Email inválido');
                // En lugar de lanzar un error, mejor lo logeo.
                $log = Zend_Registry::get('log');
                $log->warn('Email inválido: ' . $options['to']);
            }
        }
        $layouts = new Zend_Config_Ini(APPLICATION_PATH . '/configs/layoutmailing.ini', 'layouts');
        $layouts = $layouts->toArray();
        $formato = !empty($layouts[$name])?$layouts[$name]:'_layout.phtml';
        
        //Por favor no modificar esta parte ... Martin
        $correoinforma = null;
        foreach ($options as $key => $value) {
            $mailView->assign($key, $value);
            //======= cambiar ===========
            if ($key == 'correoinforma')
                $correoinforma = $value;
            //======= cambiar ===========
            $options['subject'] = str_replace('{%'.$key.'%}', $value, $options['subject']); 
        }
        $mailView->addHelperPath('App/View/Helper', 'App_View_Helper');
        $layoutView->addHelperPath('App/View/Helper', 'App_View_Helper');
        $mailViewHtml = $mailView->render($template);
        
        //Por favor no modificar esta parte ... Martin
        if ($formato=='_layout-bolsa.phtml') {
            $layoutView->assign(
                array(
                    'emailTemplate' => $mailViewHtml, 'correoinforma' => $correoinforma
                )
            );
        } else {
            $layoutView->assign('emailTemplate', $mailViewHtml);
        }
        
        //asignando el layout que tomara
        $mailHtml = $layoutView->render($formato);
        
        $this->_mail->setBodyHtml($mailHtml);
        $this->_mail->addTo($options['to']);
        $this->_mail->setSubject($options['subject']);
        $this->_mail->send();
    }

}