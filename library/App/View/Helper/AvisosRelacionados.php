<?php

class App_View_Helper_AvisosRelacionados extends Zend_View_Helper_HtmlElement
{
  
    public function AvisosRelacionados($aviso)
    {
        $db =  Zend_Db_Table::getDefaultAdapter();
        $auth = Zend_Auth::getInstance()->getIdentity();
        
        if (!isset($auth['usuario'])) {
            return false;
        }
        
        $user = $auth['usuario']->id;
        
        $sqlP = $db->select()->from('postulante', 'id')->where('id_usuario = ?', $user);
        $dataP = $db->fetchRow($sqlP);
        $sql = $db->select()->from('postulacion', 'activo')->where('id_anuncio_web = ?', $aviso)
                ->where('id_postulante = ?', (empty($dataP['id'])?'0':$dataP['id']))->where('activo = ?', 1);
        
        $data = $db->fetchCol($sql);
        
        return !empty($data);
         
    }
}