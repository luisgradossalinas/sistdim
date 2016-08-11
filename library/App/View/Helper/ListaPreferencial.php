<?php

/**
 * Retorna el HTML de la lista de avisos preferenciales
 *
 * @author jfabian
 *
 * Modificado por @jperamas
 * No retorna si es solo un aviso en vista previa
 */
class App_View_Helper_ListaPreferencial extends Zend_View_Helper_HtmlElement
{
  
    public function ListaPreferencial($idElement, $numeroAvisos, $dataPosicion = null, $dataAttribs = null, $js = true)
    {
        $values = array();
        $href = "#";
        if ($dataPosicion['data'] != null) {
            foreach ($dataPosicion['data'] as $key => $value) {
                $values[] = $value['id'];
            }
        }
        
        if ($numeroAvisos <= $dataPosicion['current']) {
            $dataPosicion['current'] = $numeroAvisos;
        }
        
        $html = "<ul id='$idElement'>";
        for ($i = 1; $i <= $numeroAvisos; $i++) {
            
            if ($i == 1) {
                $html .= "<li class='liPagerAP left firstAP'>";
            } elseif ($i == $numeroAvisos) {
                $html .= "<li class='liPagerAP left lastAP'>";
            } else {
                $html .= "<li class='liPagerAP left'>";
            }
            
            if ($i == $dataPosicion['current']) {
                $class = "currentItem";
                $href = "#";
            } elseif (
                isset($dataPosicion['data'][$i-1]['id']) && 
                in_array($dataPosicion['data'][$i-1]['id'], $values)
            ) {
                $class = "tooltipApt readyItem";
                $params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
                $href = "/".$params['module']."/"
                    .$params['controller']."/"
                    .$params['action']."/aviso/"
                    .$dataPosicion['data'][$i-1]['id'];
            } elseif ($i == count($dataPosicion['data']) + 1 && $js == true) {
                $class = 'seudoItem';
                $params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
                $href = "/".$params['module']."/"
                    .$params['controller']."/"
                    .$params['action']."/preferencial/"
                    .$dataPosicion['anuncioImpreso'];
            } else {
                $class = "normalItem";
                $href = "#";
            }
            
            if (isset($dataPosicion['data'][$i-1]['puesto'])) {
                $title = $dataPosicion['data'][$i-1]['puesto'];
            } else {
                $title = "";
            }
            
            $attribs = "";
            if ($dataAttribs != null) {
                foreach ($dataAttribs as $key => $value) {
                    if (isset($value[$i])) {
                        $attribs .= "$key = '$value[$i]' ";
                    }
                }
            }
            if ($js == true) {
                $style = '';
            } else {
                $left = 33*($i-1);
                $style = 'left: '.$left.'px;';
            }
            $html .= "<a title='$title' class='aPagerAP left $class' href='$href' $attribs style='$style' >$i</a>";
            $html .= "</li>";
        }
        $html .= "</ul>";
        //echo $html;exit;
        //return $html;
        //Joan    
        if (count($dataPosicion['data']) == '1' && 
            $dataPosicion['current'] == 1 &&
            $dataAttribs != null) {
            return ''; 
        } else {
            return $html; 
        }
    }    
}