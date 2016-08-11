<?php


/**
 * Description of ImgLadoMayor
 *
 * @author svaisman
 */
class App_View_Helper_ValidarTamanioImagen extends Zend_View_Helper_HtmlElement
{

    public function ValidarTamanioImagen($ruta, $valorMin)
    {
        $img = new ZendImage();
        $img->loadImage($ruta);
        //echo $ruta;
        //echo "w:".$img->width." - h:".$img->height; exit;
        if ($img->width < $valorMin || $img->height < $valorMin) return false;
        else return true;
    }

}