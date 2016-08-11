<?php

/**
 * Description of ImgLadoMayor
 *
 * @author svaisman
 */
class App_View_Helper_ImgLadoMayor extends Zend_View_Helper_HtmlElement
{
  
    public function ImgLadoMayor($imgName)
    {
        $img = new ZendImage();
        $img->loadImage($imgName);
        if($img->width>$img->height)
            return "width";
        else
            return "height";
    }
}