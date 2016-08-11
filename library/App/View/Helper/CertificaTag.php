<?php

/**
 * Description of Attribs
 *
 * @author eanaya
 */
class App_View_Helper_CertificaTag extends Zend_View_Helper_HtmlElement
{

    public function CertificaTag($modulo = "", $controlador = "", $accion = "")
    {
        $certifica = "";
        
        switch ($modulo) {
            case "postulante": 
                switch ($controlador) {
                    case "home": 
                        switch ($accion) {
                            case "index": 
                                $certifica = "/aptitus/portada"; 
                                break;
                            case "que-es-aptitus": 
                                $certifica = "/aptitus/que-es"; 
                                break;
                            case "porque-usar-aptitus": 
                                $certifica = "/aptitus/porque-usar"; 
                                break;
                            case "terminos-de-uso": 
                                $certifica = "/aptitus/terminos-de-uso"; 
                                break;
                            case "politica-privacidad": 
                                $certifica = "/aptitus/politica-privacidad"; 
                                break;
                        }
                        break;
                    case "buscar": 
                        $certifica = "/aptitus/buscar"; 
                        break; 
                    case "aviso": 
                        $certifica = "/aptitus/ofertas"; 
                        break; 
                    case "registro": 
                        $certifica = "/aptitus/registro"; 
                        break; 
                    case "error": 
                        $certifica = "/aptitus/mantenimiento"; //default en err
                        switch ($accion) {
                            case "error": 
                                $certifica = "/aptitus/Pagina404"; 
                                break;
                            case "page404": 
                                $certifica = "/aptitus/Pagina-Mantenimiento"; 
                                break;
                        }                        
                        break; 
                    case "postulaciones": 
                        $certifica = "/aptitus/postulaciones"; 
                        break; 
                    case "notificaciones": 
                        $certifica = "/aptitus/notificaciones"; 
                        break; 
                    case "subir-cv": 
                        $certifica = "/aptitus/subir-cv"; 
                        break; 
                    case "mi-cuenta": 
                        switch ($accion) {
                            case "index": 
                                $certifica = "/aptitus/mi-cuenta"; 
                                break;
                            case "mis-datos-personales": 
                                $certifica = "/aptitus/mi-cuenta/mis-datos-personales"; 
                                break;
                            case "mis-experiencias": 
                                $certifica = "/aptitus/mi-cuenta/mis-experiencias"; 
                                break;
                            case "mis-estudios": 
                                $certifica = "/aptitus/mi-cuenta/mis-estudios"; 
                                break;
                            case "mis-idiomas": 
                                $certifica = "/aptitus/mi-cuenta/mis-idiomas"; 
                                break;
                            case "mis-programas": 
                                $certifica = "/aptitus/mi-cuenta/mis-programas"; 
                                break;
                            case "mis-referencias": 
                                $certifica = "/aptitus/mi-cuenta/mis-referencias"; 
                                break;
                            case "mi-perfil": 
                                $certifica = "/aptitus/mi-cuenta/mi-perfil"; 
                                break;
                            case "cambio-de-clave": 
                                $certifica = "/aptitus/mi-cuenta/cambio-de-clave"; 
                                break;
                            case "redes-sociales": 
                                $certifica = "/aptitus/mi-cuenta/redes-sociales"; 
                                break;
                            case "privacidad": 
                                $certifica = "/aptitus/mi-cuenta/privacidad"; 
                                break;
                            case "mis-alertas": 
                                $certifica = "/aptitus/mi-cuenta/mis-alertas"; 
                                break;
                        }
                        break;
                }
                break;
            case "empresa":
                switch ($controlador) {
                    case "home": 
                        switch ($accion) {
                            case "index": 
                                $certifica = "/aptitus/empresa/portada"; 
                                break;
                        }
                        break;
                    
                    case "mi-cuenta": 
                        switch ($accion) {
                            case "index": 
                                $certifica = "/aptitus/empresa/mi-cuenta"; 
                                break;
                            case "datos-empresa": 
                                $certifica = "/aptitus/empresa/datos-empresa"; 
                                break;
                            case "mis-avisos": 
                                $certifica = "/aptitus/empresa/mis-avisos"; 
                                break;
                            case "cambio-clave": 
                                $certifica = "/aptitus/empresa/cambio-clave"; 
                                break;
                        }
                        break;
                    case "mis-procesos": 
                        switch ($accion) {
                            case "index": 
                                $certifica = "/aptitus/empresa/mis-procesos"; 
                                break;
                            case "procesos-cerrados": 
                                $certifica = "/aptitus/empresa/procesos-cerrados"; 
                                break;
                            case "borradores": 
                                $certifica = "/aptitus/empresa/borradores"; 
                                break;
                        }
                        break;
                    case "mi-estado-cuenta": 
                        switch ($accion) {
                            case "index": 
                                $certifica = "/aptitus/empresa/mi-estado-cuenta"; 
                                break;
                            case "en-proceso": 
                                $certifica = "/aptitus/empresa/en-proceso"; 
                                break;
                        }
                        break;
                    
                    case "administrador": 
                        $certifica = "/aptitus/empresa/administrador";
                        break; 
                    case "registro-empresa": 
                        $certifica = "/aptitus/empresa/registro-empresa";
                        break; 
                    case "publica-aviso": 
                        $certifica = "/aptitus/empresa/publicacion"; 
                        break; 
                }
                break;
        }
        
        if ($certifica == "") {
            $certifica = "/aptitus/otros";
        }
        
        //var_dump($certifica);
       
        return 
        PHP_EOL."<!-- Certifica.com -->".PHP_EOL.
        '<script type="text/javascript" src="http://c.scorecardresearch.com/certifica-js14.js">'.
        '</script>'.PHP_EOL.
        '<script type="text/javascript" src="http://c.scorecardresearch.com/certifica.js">'.
        '</script>'.PHP_EOL.
        '<script type="text/javascript">'.PHP_EOL.
        '<!--'.PHP_EOL.
        'tagCertifica(106736,"'.$certifica.'");'.PHP_EOL.
        '//'.PHP_EOL.
        '-->'.PHP_EOL.
        '</script>'.PHP_EOL;
    }

}