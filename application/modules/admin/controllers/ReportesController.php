<?php

class Admin_ReportesController extends App_Controller_Action_Admin {

    private $_puesto;
    private $_organo;
    private $_unidadOrganica;
    private $_usuario;
    private $_rol;
    private $_proyecto;

    public function init() {
        $this->_puesto = new Application_Model_Puesto;
        $this->_organo = new Application_Model_Organo;
        $this->_unidadOrganica = new Application_Model_UnidadOrganica;

        $sesion_usuario = new Zend_Session_Namespace('sesion_usuario');
        $this->_proyecto = $sesion_usuario->sesion_usuario['id_proyecto'];
        $this->_usuario = $sesion_usuario->sesion_usuario['id'];
        $this->_rol = $sesion_usuario->sesion_usuario['id_rol'];

        Zend_Layout::getMvcInstance()->assign('show', '1'); //No mostrar en el menú la barra horizontal
        parent::init();
    }

    public function organoUnidadAction() {
        Zend_Layout::getMvcInstance()->assign('active', 'Por Órgano / Unidad Orgánica');
        Zend_Layout::getMvcInstance()->assign('padre', 8);
        Zend_Layout::getMvcInstance()->assign('link', 'reporteorganounidad');

        $this->view->headScript()->appendFile(SITE_URL . '/js/reportes/organo-unidad.js');
        $this->view->organo = $this->_organo->obtenerOrgano($this->_proyecto);
    }

    public function exportWordOrganoUnidadAction() {

        $this->_helper->layout->disableLayout();
        //$this->_helper->viewRenderer->setNoRender(true);
        $data = $this->_getAllParams();
        //Previene vulnerabilidad XSS (Cross-site scripting)
        $filtro = new Zend_Filter_StripTags();
        foreach ($data as $key => $val) {
            $data[$key] = $filtro->filter(trim($val));
        }

        if (!$this->getRequest()->isXmlHttpRequest())
            exit('Acción solo válida para peticiones ajax');

        if ($this->_hasParam('unidad')) {
            $unidad = $this->_getParam('unidad');
            $dataPuesto = $this->_puesto->obtenerPuestos($unidad);
            $valorServir = (int) $this->getConfig()->valor->redondeo;
            $contador = 0;
            $tcant = 0;
            $tdota = 0;
            $dataWord = array();
            foreach ($dataPuesto as $value) {

                $dataWord[$contador]['puesto'] = $value['puesto'];
                $dataWord[$contador]['cantidad'] = $value['cantidad'];

                $tdotacion = explode('.', round($value['total_dotacion'], 2));
                if ((int) @$tdotacion[1] >= $valorServir) {
                    $tdotacion = (int) $tdotacion[0] + 1;
                } else {
                    $tdotacion = (int) $tdotacion[0];
                }

                $tcant += $value['cantidad'];
                $tdota += $tdotacion;

                $dataWord[$contador]['tdota'] = $tdotacion;
                $dataWord[$contador]['necesidades'] = $tdotacion - $value['cantidad'];
                $contador++;
            }

            $dataWord[$contador]['puesto'] = 'Total';
            $dataWord[$contador]['cantidad'] = $tcant;
            $dataWord[$contador]['tdota'] = $tdota;
            $dataWord[$contador]['necesidades'] = $tdota - $tcant;

            $nomorgano = $this->_getParam('nomorgano');
            $nomunidad = $this->_getParam('nomunidad');

            //Exportar word text
            //$this->getHelper('word')->repOrganoUnidad($dataWord, $nomorgano, $nomunidad);
            
            $PHPWord = new PHPWord();
        $section = $PHPWord->createSection();

        //Template-Organo-Unidad.docx
        //$section = $PHPWord->loadTemplate('Template-Organo-Unidad.docx');
        $styleTable = array('borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80);
        $styleFirstRow = array('borderBottomSize' => 18, 'borderBottomColor' => '000000', 'bgColor' => 'BFD7FA');

        // Define cell style arrays
        $styleCell = array('valign' => 'center');
        $styleCellBTLR = array('valign' => 'center', 'textDirection' => PHPWord_Style_Cell::TEXT_DIR_BTLR); //Texto en vertical
        // Define font style for first row
        $fontStyle = array('bold' => true, 'align' => 'center');

        $section->addText(utf8_decode("Órgano: ".$nomorgano."   Unidad Orgánica: ".$nomunidad));
        //$section->addTextBreak(1); // Enter
        // Add table style
        $PHPWord->addTableStyle('myOwnTableStyle', $styleTable, $styleFirstRow);

        // Add table
        $table = $section->addTable('myOwnTableStyle');

        $textoCenter = array('align' => 'center');
        $table->addRow(900);

        // Add cells
        $table->addCell(200, $styleCell)->addText('N', $fontStyle);
        $table->addCell(3000, $styleCell)->addText('Ejecutor', $fontStyle);
        $table->addCell(2000, $styleCell)->addText(utf8_decode('Suma Dotación Actual'), $fontStyle);
        $table->addCell(2000, $styleCell)->addText(utf8_decode('Suma según Carga de Trabajo'), $fontStyle);
        $table->addCell(2000, $styleCell)->addText(utf8_decode('Suma de Necesidades de Dotación'), $fontStyle);

        $contador = 0;
        $nreg = count($dataWord);
        foreach ($dataWord as $value) {
            $contador++;
            $table->addRow();
            if ($nreg != $contador) {
                $table->addCell(200)->addText($contador);
            } else {
                $table->addCell(200)->addText('');
            }
            $table->addCell(3000)->addText(utf8_decode($value['puesto'])); //Ejecutor
            $table->addCell(2000, $styleCell)->addText($value['cantidad'], $textoCenter); //Suma dotación atual X
            $table->addCell(2000, $styleCell)->addText($value['tdota'], $textoCenter); //Suma carga de trabajo Y
            $table->addCell(2000, $styleCell)->addText($value['necesidades'], $textoCenter); //Y-X
        }

        $filename = 'Organo-Unidad.docx';
        $objWriter = PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
        $objWriter->save($filename);

            $filename = 'Organo-Unidad.docx';
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="Organo-Unidad.docx"');
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            //header('Content-Length: ' . filesize($h2d_file_uri));
            ob_clean();
            flush();
            //readfile($h2d_file_uri);
            //unlink($h2d_file_uri);
            //exit;
        }
    }

    public function grupoFamiliaRolAction() {
        Zend_Layout::getMvcInstance()->assign('active', 'Por Grupo, Familia y Rol');
        Zend_Layout::getMvcInstance()->assign('padre', 8);
        Zend_Layout::getMvcInstance()->assign('link', 'gfrol');

        $this->view->headScript()->appendFile(SITE_URL . '/js/reportes/grupo-familia-rol.js');
        $this->view->organo = $this->_organo->obtenerOrgano($this->_proyecto);
    }
    
    public function exportWordGrupoFamiliaRolAction() {

        $this->_helper->layout->disableLayout();
        //$this->_helper->viewRenderer->setNoRender(true);
        $data = $this->_getAllParams();
        //Previene vulnerabilidad XSS (Cross-site scripting)
        $filtro = new Zend_Filter_StripTags();
        foreach ($data as $key => $val) {
            $data[$key] = $filtro->filter(trim($val));
        }

        if (!$this->getRequest()->isXmlHttpRequest())
            exit('Acción solo válida para peticiones ajax');

        if ($this->_hasParam('unidad')) {
            $unidad = $this->_getParam('unidad');
            $dataPuesto = $this->_puesto->obtenerPuestos($unidad);
            $valorServir = (int) $this->getConfig()->valor->redondeo;
            $contador = 0;
            $tcant = 0;
            $tdota = 0;
            $dataWord = array();
            foreach ($dataPuesto as $value) {

                $dataWord[$contador]['grupo'] = $value['grupo'];
                $dataWord[$contador]['familia'] = $value['familia'];
                $dataWord[$contador]['rpuesto'] = $value['rpuesto'];
                $dataWord[$contador]['puesto'] = $value['puesto'];
                $dataWord[$contador]['cantidad'] = $value['cantidad'];

                $tdotacion = explode('.', round($value['total_dotacion'], 2));
                if ((int) @$tdotacion[1] >= $valorServir) {
                    $tdotacion = (int) $tdotacion[0] + 1;
                } else {
                    $tdotacion = (int) $tdotacion[0];
                }

                $tcant += $value['cantidad'];
                $tdota += $tdotacion;

                $dataWord[$contador]['tdota'] = $tdotacion;
                $dataWord[$contador]['necesidades'] = $tdotacion - $value['cantidad'];
                $contador++;
            }
            
            $dataWord[$contador]['grupo'] = '';
            $dataWord[$contador]['familia'] = '';
            $dataWord[$contador]['rpuesto'] = '';
            $dataWord[$contador]['puesto'] = 'Total';
            $dataWord[$contador]['cantidad'] = $tcant;
            $dataWord[$contador]['tdota'] = $tdota;
            $dataWord[$contador]['necesidades'] = $tdota - $tcant;

            $nomorgano = $this->_getParam('nomorgano');
            $nomunidad = $this->_getParam('nomunidad');

            //Exportar word text
            //$this->getHelper('word')->repOrganoUnidad($dataWord, $nomorgano, $nomunidad);
            
            $PHPWord = new PHPWord();
        $section = $PHPWord->createSection();

        //Template-Organo-Unidad.docx
        //$section = $PHPWord->loadTemplate('Template-Organo-Unidad.docx');
        $styleTable = array('borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80);
        $styleFirstRow = array('borderBottomSize' => 18, 'borderBottomColor' => '000000', 'bgColor' => 'BFD7FA');

        // Define cell style arrays
        $styleCell = array('valign' => 'center');
        $styleCellBTLR = array('valign' => 'center', 'textDirection' => PHPWord_Style_Cell::TEXT_DIR_BTLR); //Texto en vertical
        // Define font style for first row
        $fontStyle = array('bold' => true, 'align' => 'center');

        $section->addText(utf8_decode("Órgano: ".$nomorgano."   Unidad Orgánica: ".$nomunidad));
        //$section->addTextBreak(1); // Enter
        // Add table style
        $PHPWord->addTableStyle('myOwnTableStyle', $styleTable, $styleFirstRow);

        // Add table
        $table = $section->addTable('myOwnTableStyle');

        $textoCenter = array('align' => 'center');
        $table->addRow(900);

        // Add cells
        $table->addCell(200, $styleCell)->addText('N', $fontStyle);
        $table->addCell(3000, $styleCell)->addText('Grupo', $fontStyle);
        $table->addCell(3000, $styleCell)->addText('Familia', $fontStyle);
        $table->addCell(3000, $styleCell)->addText('Rol', $fontStyle);
        $table->addCell(3000, $styleCell)->addText('Ejecutor', $fontStyle);
        $table->addCell(2000, $styleCell)->addText(utf8_decode('Suma Dotación Actual'), $fontStyle);
        $table->addCell(2000, $styleCell)->addText(utf8_decode('Suma según Carga de Trabajo'), $fontStyle);
        $table->addCell(2000, $styleCell)->addText(utf8_decode('Suma de Necesidades de Dotación'), $fontStyle);

        $contador = 0;
        $nreg = count($dataWord);
        foreach ($dataWord as $value) {
            $contador++;
            $table->addRow();
            if ($nreg != $contador) {
                $table->addCell(200)->addText($contador);
            } else {
                $table->addCell(200)->addText('');
            }
            $table->addCell(3000)->addText(utf8_decode($value['grupo'])); //Grupo
            $table->addCell(3000)->addText(utf8_decode($value['familia'])); //Familia
            $table->addCell(3000)->addText(utf8_decode($value['rpuesto'])); //Rol
            $table->addCell(3000)->addText(utf8_decode($value['puesto'])); //Ejecutor
            $table->addCell(2000, $styleCell)->addText($value['cantidad'], $textoCenter); //Suma dotación atual X
            $table->addCell(2000, $styleCell)->addText($value['tdota'], $textoCenter); //Suma carga de trabajo Y
            $table->addCell(2000, $styleCell)->addText($value['necesidades'], $textoCenter); //Y-X
        }

        $filename = 'GrupoFamiliaRol.docx';
        $objWriter = PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
        $objWriter->save($filename);

            $filename = 'GrupoFamiliaRol.docx';
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="GrupoFamiliaRol.docx"');
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            //header('Content-Length: ' . filesize($h2d_file_uri));
            ob_clean();
            flush();
            //readfile($h2d_file_uri);
            //unlink($h2d_file_uri);
            //exit;
        }
    }
    

    public function estadoProyectoAction() {
        Zend_Layout::getMvcInstance()->assign('active', 'Estado del proyecto');
        Zend_Layout::getMvcInstance()->assign('padre', 8);
        Zend_Layout::getMvcInstance()->assign('link', 'estproy');

        $this->view->headScript()->appendFile(SITE_URL . '/js/reportes/estado-proyecto.js');
        $this->view->organoUnidad = $this->_unidadOrganica->obtenerOrganoUOrganica($this->_proyecto);
    }

    public function analisisPertinenciaAction() {
        Zend_Layout::getMvcInstance()->assign('active', 'Reporte análisis de pertinencia');
        Zend_Layout::getMvcInstance()->assign('padre', 8);
        Zend_Layout::getMvcInstance()->assign('link', 'analpert');

        $this->view->headScript()->appendFile(SITE_URL . '/js/reportes/analisis-pertinencia.js');
        $this->view->organo = $this->_organo->obtenerOrgano($this->_proyecto);
    }

    public function dimensionamientoAction() {
        Zend_Layout::getMvcInstance()->assign('active', 'Matriz de dimensionamiento');
        Zend_Layout::getMvcInstance()->assign('padre', 8);
        Zend_Layout::getMvcInstance()->assign('link', 'dimensionamiento');
    }

}
