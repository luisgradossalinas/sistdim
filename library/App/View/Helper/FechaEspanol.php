<?php

class App_View_Helper_FechaEspanol extends Zend_View_Helper_HtmlElement
{
    /*
     * recibe parametro fecha del tipo date() pero si deseas la fecha actual
     * solo pasarle "now"
     */
    public function FechaEspanol($fecha)
    {
       $fecha = new DateTime($fecha);
       $dias = array("Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado");
       $meses = array(
           "Enero", 
           "Febrero", 
           "Marzo", 
           "Abril", 
           "Mayo", 
           "Junio", 
           "Julio", 
           "Agosto", 
           "Septiembre", 
           "Octubre", 
           "Noviembre", 
           "Diciembre"
       );
       
       $dia = $fecha->format("w");
       $ndia = $fecha->format("d");
       $nmes = $fecha->format("m");
       $ano = $fecha->format("Y");
//       return $ndia." de ".$meses[$nmes-1]." del ".$ano;
       return $dias[$dia] . " " .$ndia." de ".$meses[$nmes-1];
    }
}
