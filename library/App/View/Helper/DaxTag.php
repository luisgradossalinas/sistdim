<?php

/**
 * Description of Attribs
 *
 * @author eanaya
 */
class App_View_Helper_DaxTag extends Zend_View_Helper_HtmlElement
{

    public function DaxTag($modulo = "", $controlador = "", $accion = "", $params = array())
    {
        $config = Zend_Registry::get("config");
        $repOrigin = array(' ','ñ', 'á', 'é', 'í', 'ó', 'ú', 'Á', 'É', 'Í', 'Ó', 'Ú');
        $repRempla = array('-','n', 'a', 'e', 'i', 'o', 'u', 'A', 'E', 'I', 'O', 'U');
        $script = "";
        $tag = "";
        
        $tarifa = array(
            '2'=>'impreso-clasico-1',
            '3'=>'impreso-clasico-2',
            '4'=>'impreso-clasico-3',
            '5'=>'impreso-oro-1',
            '6'=>'impreso-oro-2',
            '7'=>'impreso-oro-3',
            '8'=>'impreso-platinum-1',
            '9'=>'impreso-platinum-2',
            '10'=>'impreso-platinum-3'
        );
        $requestURI = $params['REQUEST_URI'];
        
        unset($params['module']);
        unset($params['controller']);
        unset($params['action']);
        unset($params['REQUEST_URI']);
        //var_dump($requestURI, $params);
        
        switch ($modulo) {
            case "postulante": 
                switch ($controlador) {
                    case "home": 
                        switch ($accion) {
                            case "index": 
                                $tag = 'portada.inicio';
                                break;
                            case "que-es-aptitus": 
                                $tag = 'otros.que-es';
                                break;
                            case "porque-usar-aptitus": 
                                $tag = 'otros.porque-usar';
                                break;
                            case "contactenos":
                                $tag = 'otros.contactenos';
                                break;
                            case "terminos-de-uso": 
                                $tag = 'otros.terminos-de-uso';
                                break;
                            case "politica-privacidad": 
                                $tag = 'otros.politicas-de-privacidad';
                                break;
                        }
                        break;
                    case "buscar": 
                        $tag = 'busqueda.portada';
                        $nroreg = count($params);
                        $requestURI = str_replace('/', '', $requestURI);
                        if ($nroreg == 1 || ($nroreg==2 && !empty($params['page']))) {
                            if (isset($params[$config->busqueda->urls->areas])) {
                                $area = $params[$config->busqueda->urls->areas];
                                if ($area != '') {
                                    if (strpos($area, '--') === false) {
                                        $area = str_replace($repOrigin, $repRempla, $area);
                                        $tag = 'areas.'.$area.'.portada';
                                    }
                                }
                            } elseif (isset($params[$config->busqueda->urls->nivel])) {
                                $nivel = $params[$config->busqueda->urls->nivel];
                                if ($nivel != '') {
                                    if (strpos($nivel, '--') === false) {
                                        $nivel = str_replace($repOrigin, $repRempla, $nivel);
                                        $tag = 'nivel.'.$nivel.'.portada';
                                    }
                                }
                            } elseif (isset($params[$config->busqueda->urls->ubicacion])) {
                                $ubica = $params[$config->busqueda->urls->ubicacion];
                                if ($ubica != '') {
                                    if (strpos($ubica, '--') === false) {
                                        $ubica = str_replace($repOrigin, $repRempla, $ubica);
                                        $tag = 'ubicacion.'.$ubica.'.portada';
                                    }
                                }
                            } elseif (isset($params[$config->busqueda->urls->query])) {
                                $ubica = $params[$config->busqueda->urls->query];
                                $tag = 'busqueda.portada';
                            } elseif (isset($params[$config->busqueda->urls->fechapub])) {
                                $fechapub = $params[$config->busqueda->urls->fechapub];
                                if ($fechapub != '') {
                                    if (strpos($fechapub, '--') === false) {
                                        $fechapub = str_replace($repOrigin, $repRempla, $fechapub);
                                        $tag = 'busqueda.otros.'.
                                            $config->busqueda->urls->fechapub.'-'.$fechapub;
                                    }
                                }
                            } elseif (isset($params[$config->busqueda->urls->remuneracion])) {
                                $remuneracion = $params[$config->busqueda->urls->remuneracion];
                                if ($remuneracion != '') {
                                    if (strpos($remuneracion, '--') === false) {
                                        $remuneracion = str_replace($repOrigin, $repRempla, $remuneracion);
                                        $tag = 'busqueda.otros.'.
                                            $config->busqueda->urls->remuneracion.'-'.$remuneracion;
                                    }
                                }
                            }
                        } elseif ($nroreg > 1) {
                            $tag = 'busqueda.otros.';
                            foreach ($params as $key => $value) {
                                $value = str_replace($repOrigin, $repRempla, $value);
                                $tag .= $key.'-'.$value.'-';
                            }
                            $tag = substr($tag, 0, -1);
                        }
                        if ($requestURI=='buscarnivel') {
                            $tag = 'nivel.portada';
                        }
                        if ($requestURI=='buscarareas') {
                            $tag = 'areas.portada';
                        }
                        if ($requestURI=='buscarubicacion') {
                            $tag = 'ubicacion.portada';
                        }
                        break; 
                    case "aviso": 
                        switch ($accion) {
                            case "ver": 
                                $objAnuncioW = new Application_Model_AnuncioWeb();
                                $nameArea = $objAnuncioW->getNombreAreaByUrlId($params['url_id']);
                                $nameArea = str_replace($repOrigin, $repRempla, $nameArea);
                                $tag = 'areas.'.$nameArea.'.'.$params['slug'].'-'.$params['url_id'];
                                break;
                            case "": 
                                break;
                        }
                        break; 
                    case "registro": 
                        $tag = 'otros.registro';
                        break; 
                    case "error": 
                        $tag = 'otros.404'; //default en err
                        /*switch ($accion) {
                            case "error": 
                                $tag = 'otros.404';
                                break;
                            case "page404": 
                                $tag = 'otros.404';
                                break;
                        }*/
                        break; 
                    case "postulaciones": 
                        $tag = 'sesion.postulaciones';
                        break; 
                    case "notificaciones": 
                        $tag = 'sesion.notificaciones';
                        break; 
                    case "subir-cv": 
                        $tag = 'sesion.subir-cv';
                        break; 
                    case "mi-cuenta": 
                        switch ($accion) {
                            case "index": 
                                $tag = 'sesion.portada';
                                break;
                            case "mis-datos-personales": 
                                $tag = 'sesion.perfil.portada';
                                if (!empty($params['datos'])) {
                                    $tag = 'sesion.datos-personales.portada';
                                }
                                break;
                            case "mis-experiencias": 
                                $tag = 'sesion.perfil.experiencia';
                                break;
                            case "mis-estudios": 
                                $tag = 'sesion.perfil.estudios';
                                break;
                            case "mis-idiomas": 
                                $tag = 'sesion.perfil.idiomas';
                                break;
                            case "mis-programas": 
                                $tag = 'sesion.perfil.programas';
                                break;
                            case "mis-referencias": 
                                $tag = 'sesion.perfil.referencia';
                                break;
                            case "mi-perfil": 
                                $tag = 'sesion.perfil.mi-perfil';
                                break;
                            case "perfil-publico":
                                $tag = 'sesion.perfil.publico';
                                break;
                            case "cambio-de-clave": 
                                $tag = 'sesion.datos-personales.cambio-de-clave';
                                break;
                            case "redes-sociales": 
                                $tag = 'sesion.datos-personales.redes-sociales';
                                break;
                            case "privacidad": 
                                $tag = 'sesion.datos-personales.privacidad';
                                break;
                            case "mis-alertas": 
                                $tag = 'sesion.datos-personales.alertas';
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
                                $tag = 'empresa.portada';
                                break;
                            case "que-es-aptitus-empresa":
                                $tag = 'empresa.que-es';
                                break;
                            case "nuevo-en-aptitus":
                                $tag = 'empresa.nuevo-en-aptitus';
                                break;
                        }
                        break;
                    
                    case "mi-cuenta": 
                        switch ($accion) {
                            case "index": 
                                $tag = 'empresa.sesion.portada';
                                break;
                            case "datos-empresa": 
                                $tag = 'empresa.sesion.mis-datos.empresa';
                                break;
                            case "mis-avisos": 
                                $tag = 'empresa.sesion.mis-datos.mis-avisos';
                                if (!empty($params['inactivos'])) {
                                    $tag = 'empresa.sesion.mis-datos.mis-avisos-inactivos';
                                }
                                break;
                            case "cambio-clave": 
                                $tag = 'empresa.sesion.mis-datos.cambio-de-clave';
                                break;
                        }
                        break;
                    case "mis-procesos": 
                        switch ($accion) {
                            case "index": 
                                $tag = 'empresa.sesion.procesos.portada';
                                break;
                            case "procesos-cerrados": 
                                $tag = 'empresa.sesion.procesos.procesos-cerrados';
                                break;
                            case "borradores": 
                                $tag = 'empresa.sesion.procesos.borradores';
                                break;
                            case "ver-proceso":
                                $tag = 'empresa.sesion.procesos.ver-proceso';
                                if (!empty($params['categoria'])) {
                                    $cat = $params['categoria'];
                                    $objCatPostula = new Application_Model_CategoriaPostulacion();
                                    $authStorage = Zend_Auth::getInstance()->getStorage()->read();
                                    $category = $objCatPostula->
                                        getNombreCategoriaPost($authStorage['empresa']['id'], $cat);
                                    $category = !empty($category['nombre'])?strtolower($category['nombre']):'';
                                    switch ($category) {
                                        case 'pre-seleccionados':
                                            $tag = 'empresa.sesion.procesos.pre-selecionado';
                                            break;
                                        case 'seleccionados':
                                            $tag = 'empresa.sesion.procesos.seleccionado';
                                            break;
                                        case 'finalistas':
                                            $tag = 'empresa.sesion.procesos.finalista';
                                            break;
                                    }
                                }
                                $script = 'function digital_analytix_tag_descartados(){'.
                                    " dax_seo_ajax".
                                    "('empresa.sesion.procesos.descartados'); }".PHP_EOL.
                                    'function digital_analytix_tag_perfil(){'.
                                    " dax_seo_ajax".
                                    "('empresa.sesion.procesos.perfil'); }".PHP_EOL;
                                break;
                        }
                        break;
                    case "mi-estado-cuenta": 
                        switch ($accion) {
                            case "index": 
                                $tag = 'empresa.sesion.estado-de-cuenta.portada';
                                break;
                            case "en-proceso": 
                                $tag = 'empresa.sesion.estado-de-cuenta.proceso';
                                break;
                        }
                        break;
                    
                    case "administrador": 
                        $tag = 'empresa.sesion.mis-datos.administrador';
                        break; 
                    case "registro-empresa": 
                        $tag = 'empresa.registro';
                        break; 
                    case "publica-aviso": 
                        switch ($accion) {
                            case "paso1": 
                                $tag = 'empresa.publicacion.paso1';
                                break;
                            case "paso2":
                                $tag = 'empresa.publicacion.paso2.publicacion-web-gratis';
                                if (!empty($params['tarifa'])) {
                                    $tarifa = $params['tarifa'];
                                    switch ($tarifa) {
                                        case '1':
                                            break;
                                        case '2':
                                            $tag = 'empresa.publicacion.paso2.impreso-clasico-1';
                                            break;
                                        case '3':
                                            $tag = 'empresa.publicacion.paso2.impreso-clasico-2';
                                            break;
                                        case '4':
                                            $tag = 'empresa.publicacion.paso2.impreso-clasico-3';
                                            break;
                                        case '5':
                                            $tag = 'empresa.publicacion.paso2.impreso-oro-1';
                                            break;
                                        case '6':
                                            $tag = 'empresa.publicacion.paso2.impreso-oro-2';
                                            break;
                                        case '7':
                                            $tag = 'empresa.publicacion.paso2.impreso-oro-3';
                                            break;
                                        case '8':
                                            $tag = 'empresa.publicacion.paso2.impreso-platinum-1';
                                            break;
                                        case '9':
                                            $tag = 'empresa.publicacion.paso2.impreso-platinum-2';
                                            break;
                                        case '10':
                                            $tag = 'empresa.publicacion.paso2.impreso-platinum-3';
                                            break;
                                    }
                                }
                                break;
                            case "paso3":
                                if (!empty($params['aviso'])) {
                                    $avisoId = $params['aviso'];
                                    $objAnuncioW = new Application_Model_AnuncioWeb();
                                    $idtarif = $objAnuncioW->getIdTarifaByIdAnuncio($avisoId);
                                    $tag = 'empresa.publicacion.paso3.'.$tarifa[$idtarif];
                                }
                                break;
                            case "paso4":
                                if (!empty($params['aviso'])) {
                                    $avisoId = $params['aviso'];
                                    $objAnuncioW = new Application_Model_AnuncioWeb();
                                    $idtarif = $objAnuncioW->getIdTarifaByIdAnuncio($avisoId);
                                    $tag = 'empresa.publicacion.paso4.'.$tarifa[$idtarif];
                                }
                                break;
                        }
                        break;
                    case "comprar-aviso":
                        switch ($accion) {
                            case "pago-efectivo": 
                                $tag = 'empresa.publicacion.pago-efectivo';
                                break;
                        }
                        break;
                }
                break;
            case 'admin':
                switch ($controlador) {
                    case 'home':
                        switch ($accion) {
                            case 'index':
                                $tag = 'administrador.login';
                                break;
                        }
                        break;
                    case 'gestion':
                        $tag = 'administrador.portada';
                        switch ($accion) {
                            case 'usuarios-admin':
                                $tag = 'administrador.administrador-de-usuarios';
                                break;
                            case 'cambio-clave':
                                $tag = 'administrador.cambio-de-clave';
                                break;
                            case 'postulantes':
                                $tag = 'administrador.postulantes';
                                break;
                            case 'avisos':
                                $tag = 'administrador.avisos';
                                break;
                            case 'empresas':
                                $tag = 'administrador.empresas';
                                break;
                            case 'avisos-preferenciales':
                                $tag = 'administrador.avisos-preferenciales';
                                break;
                            case 'callcenter':
                                $tag = 'administrador.call-center';
                                break;
                        }
                        break;
                }
                break;
        }
        
        if ($tag == "") {
            $tag = "otros.otros";
        }
        
        //var_dump($tag, $script);
       
        return 
        PHP_EOL.'<!-- Begin comScore UDM code 1.1104.26 --> '.PHP_EOL.
        '<script type="text/javascript">'.PHP_EOL.
        '// <![CDATA[ '.PHP_EOL.
        'function comScore(t){var b="comScore",o=document,f=o.location,a="",e="undefined",'.
        'g=2048,s,k,p,h,r="characterSet",n="defaultCharset",m=(typeof encodeURIComponent!=e'.
        '?encodeURIComponent:escape);if(o.cookie.indexOf(b+"=")!=-1){p=o.cookie.split(";");'.
        'for(h=0,f=p.length;h<f;h++){var q=p[h].indexOf(b+"=");if(q!=-1){a="&"+'.
        'unescape(p[h].substring(q+b.length+1))}}}t=t+"&ns__t="+(new Date().getTime());'.
        't=t+"&ns_c="+(o[r]?o[r]:(o[n]?o[n]:""))+"&c8="+m(o.title)+a+"&c7="+m(f&&f.href?f.href:o.URL)+'.
        '"&c9="+m(o.referrer);if(t.length>g&&t.indexOf("&")>0){s=t.substr(0,g-8).lastIndexOf("&");t='.
        '(t.substring(0,s)+"&ns_cut="+m(t.substring(s+1))).substr(0,g)}if(o.images){k=new Image();'.
        'if(typeof ns_p==e){ns_p=k}k.src=t}else{'.
        "o.write('<p><'+'img src=".'"'."'+t+'".'" height="1" width="1" alt="*"></p>'."')".
        '}}; '.PHP_EOL.
        "comScore('http'+(document.location.href.charAt(4)=='s'?'s://sb':'://b')+'.scorecardresearch.com/".
        "p?c1=2&c2=6906602&ns_site=clasificados-aptitus&name=".$tag."'); ".PHP_EOL.
        '// ]]> '.PHP_EOL.
        '</script> '.PHP_EOL.
        '<noscript><p><img src="http://b.scorecardresearch.com/p?c1=2&amp;c2=6906602&amp; '.
        'ns_site=clasificados-aptitus&amp;name='.$tag.'" height="1" width="1" alt="*"></p></noscript> '.PHP_EOL.
        '<!-- End comScore UDM code -->'.PHP_EOL.
        (!empty($script)?
            '<script type = "text/javascript" >'.PHP_EOL.
            '// <![CDATA['.PHP_EOL.'function dax_seo_ajax(name) {'.PHP_EOL.
            "(typeof comScore != 'undefined') && comScore('http'+(document.location.href.charAt(4)=='s'?".
            "'s://sb':'://b')+'.scorecardresearch.com/p?c1=2&c2=6906529&".
            "ns_site=banco-de-bogota&name='+name);".PHP_EOL.
            '}'.PHP_EOL.'// ]]>'.PHP_EOL.$script.'</script>':'');
    }

}