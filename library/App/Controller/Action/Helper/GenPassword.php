<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UtilFiles
 *
 * @author Julio Florian
 */
class App_Controller_Action_Helper_GenPassword extends Zend_Controller_Action_Helper_Abstract
{

    public function _genPassword($length = 8)
    {
        $cset = 'aeuybdghjmnpqrstvz23456789';
        $password = '';
        //srand($this->make_seed());
        for ($i = 0; $i < $length; $i++) {
            srand($this->make_seed());
            $password .= $cset[(rand(1, rand(1, 1000000)) % strlen($cset))];
        }
        return $password;
    }
    
    function make_seed()
    {
      list($usec, $sec) = explode(' ', microtime());
      $number = rand(1, rand(1, 1000000));
      $number = $number * 0.178;
      return (float) $sec + ((float) $usec * 100000) + $number;
    }
    
    
}
