<?php

class App_View_Helper_CacheStatic extends Zend_View_Helper_HtmlElement
{
    public function CacheStatic($headScript, $excepciones)
    {
        $config = Zend_Registry::get("config");
        foreach($headScript as $item):
            if (count($item->attributes)>0) {
                $valida = 0;
                for ($i=0;$i<count($excepciones);$i++) {
                    if (count(explode($excepciones[$i], $item->attributes["src"]))==2) {
                            $valida=1; break;
                    }
                }
                if ($valida==0) $item->attributes["src"] = $item->attributes["src"]."?".
                        $config->confpaginas->staticVersion;
            }
        endforeach;
    }
}