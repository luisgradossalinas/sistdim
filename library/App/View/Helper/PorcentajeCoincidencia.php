<?php

/**
 * Description of Hace
 *
 * @author svaisman
 */
class App_View_Helper_PorcentajeCoincidencia extends Zend_View_Helper_HtmlElement
{

    public function PorcentajeCoincidencia($idAnuncio, $idPostulante)
    {
        $modelo = new Application_Model_AnuncioWeb();
        $result = $modelo->porcentajeCoincidencia($idAnuncio, $idPostulante);
        return $result[0]["aptitus_match"];
    }
    
}