<?php


/**
 * Description of percentage of Perfil
 *
 * @author tavo
 */
class App_View_Helper_PorcentajePerfil
    extends Zend_View_Helper_Abstract
{

    protected $_idPostulante;

    public function PorcentajePerfil($idPostulante)
    {
        $this->_idPostulante = $idPostulante;

        $postulante = new Application_Model_Postulante();
        $_experiencia = new Application_Model_Experiencia();
        $_estudios = new Application_Model_Estudio();
        $_idiomas = new Application_Model_DominioIdioma();
        $_programas = new Application_Model_DominioProgramaComputo();

        $arrayPostulante = $postulante->getPostulante($idPostulante);

        $_foto = $arrayPostulante["path_foto"];

        $_presentacion = $arrayPostulante["presentacion"];
        $_tuweb = $arrayPostulante["website"];
        $_pathcv = $arrayPostulante["path_cv"];

        $nex = count($_experiencia->getExperiencias($this->_idPostulante));

        $nes = count($_estudios->getEstudios($this->_idPostulante));
        $nid = count($_idiomas->getDominioIdioma($this->_idPostulante));
        $npc = count($_programas->getDominioProgramaComputo($this->_idPostulante));

        $porcentaje = 0;

        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/aptitus.ini');

        if ($_pathcv != "") {
            $porcentaje+=$config->dashboard->peso->subircv;
        }

        if ($nex > 0) {
            $porcentaje+=$config->dashboard->peso->experiencia;
        }

        if ($nes > 0) {
            $porcentaje+=$config->dashboard->peso->estudios;
        }

        if ($nid > 0) {
            $porcentaje+=$config->dashboard->peso->idiomas;
        }

        if ($npc > 0) {
            $porcentaje+=$config->dashboard->peso->programas;
        }

        if ($_presentacion != "") {
            $porcentaje+=$config->dashboard->peso->presentacion;
        }

        if ($_tuweb != "") {
            $porcentaje+=$config->dashboard->peso->tuweb;
        }

        if ($_foto != "photoDefault.jpg" && $_foto != "") {
            $porcentaje+=$config->dashboard->peso->foto;
        }

        return $porcentaje;
    }

}