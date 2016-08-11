<?php

/**
 * Description of Attribs
 *
 * @author eanaya
 */
class App_View_Helper_EplaningTag extends Zend_View_Helper_HtmlElement
{

    public function EplaningTag($modulo = "", $controlador = "", $accion = "")
    {
        $config = Zend_Registry::get("config");
        $mediaurl = $config->app->mediaUrl;
        $tag = ""; $script='';
        
        switch ($modulo) {
            case "postulante": 
                switch ($controlador) {
                    case "home": 
                        switch ($accion) {
                            case "index": 
                                $tag = 'e-planning-home.js?v=2';
                                $script = '<script type="text/javascript">eplAD4M("Right1");</script>'.PHP_EOL.
                                    '<script type="text/javascript">eplAD4M("Middle");</script>'.PHP_EOL.
                                    '<script type="text/javascript">eplAD4M("Top");</script>'.PHP_EOL;
                                break;
                            default:
                                $tag = 'e-planning-internas.js';
                                $script = '<script type="text/javascript">eplAD4M("Right1");</script>'.PHP_EOL.
                                    '<script type="text/javascript">eplAD4M("Top");</script>'.PHP_EOL;
                                break;  
                        }
                        break;
                    case "buscar": 
                        $tag = 'e-planning-busqueda.js';
                        $script = '<script type="text/javascript">eplAD4M("Right1");</script>'.PHP_EOL.
                            '<script type="text/javascript">eplAD4M("Middle");</script>'.PHP_EOL.
                            '<script type="text/javascript">eplAD4M("Top");</script>'.PHP_EOL;
                        break; 
                    case "aviso": 
                        switch ($accion) {
                            case "ver": 
                                $tag = 'e-planning-detalle-aviso.js';
                                $script = '<script type="text/javascript">eplAD4M("Right1");</script>'.PHP_EOL.
                                    '<script type="text/javascript">eplAD4M("Top");</script>'.PHP_EOL;
                                break;
                        }
                        break; 
                    case "error": 
                        //$tag = 'otros.404'; //default en err
                        break; 
                    default:
                        $tag = 'e-planning-internas.js';
                        $script = '<script type="text/javascript">eplAD4M("Right1");</script>'.PHP_EOL.
                            '<script type="text/javascript">eplAD4M("Top");</script>'.PHP_EOL;
                        break;
                }
                break;
            case "empresa":
                switch ($controlador) {
                    case "home": 
                        switch ($accion) {
                            case "index": 
                                $tag = 'e-planning-home.js';
                                $script = '<script type="text/javascript">eplAD4M("Right1");</script>'.PHP_EOL.
                                    '<script type="text/javascript">eplAD4M("Middle");</script>'.PHP_EOL.
                                    '<script type="text/javascript">eplAD4M("Top");</script>'.PHP_EOL;
                                break;
                        }
                        break;
                    default:
                        $tag = 'e-planning-internas.js';
                        $script = '<script type="text/javascript">eplAD4M("Right1");</script>'.PHP_EOL.
                            '<script type="text/javascript">eplAD4M("Top");</script>'.PHP_EOL;
                        break;
                }
                break;
        }
        
        if ($tag == "") {
            //$tag = "otros.otros";
        }
        
        //var_dump($tag);
        
        return 
        PHP_EOL.'<script type="text/javascript" src="'.
            $mediaurl.'/js/'.$tag.'"></script>'.PHP_EOL;
            //$script;
    }

}
