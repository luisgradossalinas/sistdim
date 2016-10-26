<?php

class Admin_ReportesController extends App_Controller_Action_Admin {

    private $_puesto;
    private $_organo;
    private $_unidadOrganica;
    private $_proceson0;
    private $_usuario;
    private $_rol;
    private $_proyecto;
    private $_mapaPuesto;

    public function init() {
        $this->_puesto = new Application_Model_Puesto;
        $this->_organo = new Application_Model_Organo;
        $this->_unidadOrganica = new Application_Model_UnidadOrganica;
        $this->_proceson0 = new Application_Model_Proceso0;

        $sesion_usuario = new Zend_Session_Namespace('sesion_usuario');
        $this->_proyecto = $sesion_usuario->sesion_usuario['id_proyecto'];
        $this->_usuario = $sesion_usuario->sesion_usuario['id'];
        $this->_rol = $sesion_usuario->sesion_usuario['id_rol'];
        $this->_mapaPuesto = $sesion_usuario->sesion_usuario['mapa_puesto'];

        Zend_Layout::getMvcInstance()->assign('show', '1'); //No mostrar en el menú la barra horizontal
        parent::init();
    }

    public function resumenDotacionAction() {
        Zend_Layout::getMvcInstance()->assign('active', 'Resumen de dotación');
        Zend_Layout::getMvcInstance()->assign('padre', 8);
        Zend_Layout::getMvcInstance()->assign('link', 'resu_dotacion');

        $this->view->headScript()->appendFile(SITE_URL . '/js/reportes/resumen-dotacion.js');
        $this->view->organo = $this->_organo->obtenerOrgano($this->_proyecto);
    }

    public function exportWordDotacionAction() {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
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
            $dataPuesto = $this->_puesto->obtenerPuestoDotacion($unidad);
            $valorServir = (int) $this->getConfig()->valor->redondeo;
            $contador = 0;
            $tcant = 0;
            $tdota = 0;
            $dataWord = array();
            $nombrePuesto = array();
            foreach ($dataPuesto as $value) {

                $dataWord[$contador]['puesto'] = $value['puesto'];

                $tdotacion = explode('.', round($value['dotacion'], 2));
                if ((int) @$tdotacion[1] >= $valorServir) {
                    $tdotacion = (int) $tdotacion[0] + 1;
                } else {
                    $tdotacion = (int) $tdotacion[0];
                }

                //$indice = array_search($value['puesto'], $nombrePuesto);
                if (in_array($value['puesto'], $nombrePuesto)) {
                    $value['cantidad'] = 0;
                }
                $nombrePuesto[] = $value['puesto'];

                $tcant += $value['cantidad'];
                $tdota += $tdotacion;

                $dataWord[$contador]['cantidad'] = $value['cantidad'];
                $dataWord[$contador]['tdota'] = $tdotacion;
                $dataWord[$contador]['necesidades'] = $tdotacion - $value['cantidad'];
                $dataWord[$contador]['nombre_puesto'] = $value['nombre_puesto'];
                $contador++;
            }

            $dataWord[$contador]['puesto'] = 'Total';
            $dataWord[$contador]['cantidad'] = $tcant;
            $dataWord[$contador]['tdota'] = $tdota;
            $dataWord[$contador]['necesidades'] = $tdota - $tcant;
            $dataWord[$contador]['nombre_puesto'] = '';

            $nomorgano = $this->_getParam('nomorgano');
            $nomunidad = $this->_getParam('nomunidad');
            $PHPWord = new PHPWord();
            $section = $PHPWord->createSection();

            $styleTable = array('borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80);
            $styleFirstRow = array('borderBottomSize' => 18, 'borderBottomColor' => '000000', 'bgColor' => 'BFD7FA');

            // Define cell style arrays
            $styleCell = array('valign' => 'center');
            $styleCellBTLR = array('valign' => 'center', 'textDirection' => PHPWord_Style_Cell::TEXT_DIR_BTLR); //Texto en vertical
            // Define font style for first row
            $fontStyle = array('bold' => true, 'align' => 'center');

            $section->addText(utf8_decode("Órgano: " . $nomorgano . "   Unidad Orgánica: " . $nomunidad));
            //$section->addTextBreak(1); // Enter
            // Add table style
            $PHPWord->addTableStyle('myOwnTableStyle', $styleTable, $styleFirstRow);

            // Add table
            $table = $section->addTable('myOwnTableStyle');

            $textoCenter = array('align' => 'center');
            $table->addRow(900);

            // Add cells
            $table->addCell(200, $styleCell)->addText('N', $fontStyle);
            $table->addCell(3000, $styleCell)->addText('Nombre del puesto actual', $fontStyle);
            $table->addCell(2000, $styleCell)->addText(utf8_decode('Dotación Actual'), $fontStyle);
            $table->addCell(2000, $styleCell)->addText(utf8_decode('Dotación calculada'), $fontStyle);
            $table->addCell(2000, $styleCell)->addText(utf8_decode('Necesidades Dotación'), $fontStyle);
            $table->addCell(2000, $styleCell)->addText(utf8_decode('Nombre del puesto propuesto'), $fontStyle);

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
                $table->addCell(2000, $styleCell)->addText($value['nombre_puesto'], $textoCenter);
            }

            $filename = 'Resumen-Dotacion.docx';
            $objWriter = PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
            $objWriter->save($filename);

            echo Zend_Json::encode(array("success" => 1));
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
        $this->_helper->viewRenderer->setNoRender(true);

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

            $PHPWord = new PHPWord();
            $section = $PHPWord->createSection();

            $styleTable = array('borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80);
            $styleFirstRow = array('borderBottomSize' => 18, 'borderBottomColor' => '000000', 'bgColor' => 'BFD7FA');

            // Define cell style arrays
            $styleCell = array('valign' => 'center');
            $styleCellBTLR = array('valign' => 'center', 'textDirection' => PHPWord_Style_Cell::TEXT_DIR_BTLR); //Texto en vertical
            // Define font style for first row
            $fontStyle = array('bold' => true, 'align' => 'center');

            $section->addText(utf8_decode("Órgano: " . $nomorgano . "   Unidad Orgánica: " . $nomunidad));
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

            echo Zend_Json::encode(array("success" => 1));
        }
    }

    public function estadoProyectoAction() {
        Zend_Layout::getMvcInstance()->assign('active', 'Estado del proyecto');
        Zend_Layout::getMvcInstance()->assign('padre', 8);
        Zend_Layout::getMvcInstance()->assign('link', 'estproy');

        $this->view->headScript()->appendFile(SITE_URL . '/js/reportes/estado-proyecto.js');
        $data = $this->_unidadOrganica->obtenerOrganoUOrganica($this->_proyecto);

        $contador = 0;
        foreach ($data as $value) {

            $data[$contador]['dotacion'] = $this->_puesto->puestosSinDotacion($value['id_uorganica']);
            $data[$contador]['pertinencia'] = $this->_puesto->puestosSinPertinencia($value['id_uorganica']);
            $contador++;
        }

        $this->view->organoUnidad = $data;
    }

    public function resumenPertinenciaAction() {
        Zend_Layout::getMvcInstance()->assign('active', 'Resumen de pertinencia');
        Zend_Layout::getMvcInstance()->assign('padre', 8);
        Zend_Layout::getMvcInstance()->assign('link', 'analpert');

        $this->view->headScript()->appendFile(SITE_URL . '/js/reportes/resumen-pertinencia.js');
        $this->view->organo = $this->_organo->obtenerOrgano($this->_proyecto);
    }

    public function exportWordPertinenciaAction() {

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
            $dataPuesto = $this->_puesto->obtenerPuestoPertinencia($unidad);

            $nomorgano = $this->_getParam('nomorgano');
            $nomunidad = $this->_getParam('nomunidad');

            $PHPWord = new PHPWord();
            $section = $PHPWord->createSection();

            $styleTable = array('borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80);
            $styleFirstRow = array('borderBottomSize' => 18, 'borderBottomColor' => '000000', 'bgColor' => 'BFD7FA');

            // Define cell style arrays
            $styleCell = array('valign' => 'center');
            $styleCellBTLR = array('valign' => 'center', 'textDirection' => PHPWord_Style_Cell::TEXT_DIR_BTLR); //Texto en vertical
            // Define font style for first row
            $fontStyle = array('bold' => true, 'align' => 'center');

            $section->addText(utf8_decode("Órgano: " . $nomorgano . "   Unidad Orgánica: " . $nomunidad));
            //$section->addTextBreak(1); // Enter
            // Add table style
            $PHPWord->addTableStyle('myOwnTableStyle', $styleTable, $styleFirstRow);
            $table = $section->addTable('myOwnTableStyle');
            $table->addRow(900);

            // Add cells
            $table->addCell(200, $styleCell)->addText('N', $fontStyle);
            $table->addCell(3000, $styleCell)->addText('Nombre del puesto actual', $fontStyle);
            $table->addCell(3000, $styleCell)->addText('Nombre del Puesto propuesto', $fontStyle);
            $table->addCell(3000, $styleCell)->addText('Total', $fontStyle);

            $contador = 0;
            foreach ($dataPuesto as $value) {

                $valorServir = (int) $this->getConfig()->valor->redondeo;
                $tdotacion = explode('.', round($value['dotacion'], 2));
                if ((int) @$tdotacion[1] >= $valorServir) {
                    $tdotacion = (int) $tdotacion[0] + 1;
                } else {
                    $tdotacion = (int) $tdotacion[0];
                }

                $contador++;
                $table->addRow();
                $table->addCell(200)->addText($contador);
                $table->addCell(3000)->addText(utf8_decode($value['puesto']));
                $table->addCell(3000)->addText(utf8_decode($value['nombre_puesto']));
                $table->addCell(3000)->addText(utf8_decode($tdotacion));
            }

            $filename = 'Pertinencia.docx';
            $objWriter = PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
            $objWriter->save($filename);

            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender();

            echo Zend_Json::encode(array("success" => 1));
        }
    }

    public function dimensionamientoAction() {

        Zend_Layout::getMvcInstance()->assign('active', 'Matriz de dimensionamiento');
        Zend_Layout::getMvcInstance()->assign('padre', 8);
        Zend_Layout::getMvcInstance()->assign('link', 'dimensionamiento');
    }

    public function mapeoPuestoAction() {

        $objPHPExcel = new PHPExcel();

        $objPHPExcel->getProperties()->setCreator('Xperta Gestión Empresarial')
                ->setTitle('PHPExcel Test Document')
                ->setSubject('PHPExcel Test Document')
                ->setDescription('Mapeo de puestos')
                ->setKeywords('office PHPExcel php')
                ->setCategory('Test result file');
        $objPHPExcel->getActiveSheet()->setTitle('MapeoPuestos');
        $objPHPExcel->setActiveSheetIndex(0);

        //$objPHPExcel->getActiveSheet()->getRowDimension('7')->setRowHeight(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15); //->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(22);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Núm Correlativo')
                ->setCellValue('B1', 'Naturaleza del Órgano')
                ->setCellValue('C1', 'Órgano')
                ->setCellValue('D1', 'Unidad Orgánica')
                ->setCellValue('E1', 'Nombre del Puesto')
                ->setCellValue('F1', 'Cantidad de ocupados')
                ->setCellValue('G1', 'Grupo')
                ->setCellValue('H1', 'Familia')
                ->setCellValue('I1', 'Rol')
                ->setCellValue('J1', 'Nombres');

        $objPHPExcel->getActiveSheet()->getStyle('A1:J1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objPHPExcel->getActiveSheet()->getStyle('A1:J1')->getFill()->getStartColor()->setARGB('FF808080');

        $data = $this->_puesto->obtenerMapeoPuesto($this->_proyecto);
        $finalData = array();
        $contador = 0;
        foreach ($data AS $row) {
            $contador++;
            $numCorre = $row["num_correlativo"];
            if ($this->_mapaPuesto == 1) {
                $numCorre = $contador;
            }

            $finalData[] = array(
                $numCorre,
                ($row["naturaleza"]),
                ($row["organo"]),
                ($row["unidad"]),
                ($row["puesto"]),
                ($row["cantidad"]),
                ($row["grupo"]),
                ($row["familia"]),
                ($row["rpuesto"]),
                ($row["nombre_personal"])
            );
        }

        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => 'FF000000'),
                )
            ),
        );

        $objPHPExcel->getActiveSheet()->fromArray($finalData, NULL, 'A2');
        $nReg = count($finalData) + 1;

        $objPHPExcel->getActiveSheet()->getStyle('A1:J' . $nReg)->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('A1:J1')->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(0);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="MapeoPuestos.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function inventarioProcesosAction() {

        $objPHPExcel = new PHPExcel();

        $objPHPExcel->getProperties()->setCreator('Xperta Gestión Empresarial')
                ->setTitle('PHPExcel Test Document')
                ->setSubject('PHPExcel Test Document')
                ->setDescription('Mapeo de puestos')
                ->setKeywords('office PHPExcel php')
                ->setCategory('Test result file');
        $objPHPExcel->getActiveSheet()->setTitle('Inventario');
        $objPHPExcel->setActiveSheetIndex(0);

        //$objPHPExcel->getActiveSheet()->getRowDimension('7')->setRowHeight(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(8); //->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(8);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(8);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(8);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(50);

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Tipo proceso')
                ->setCellValue('B1', 'Cod 0')
                ->setCellValue('C1', 'Nivel 0')
                ->setCellValue('D1', 'Cod 1')
                ->setCellValue('E1', 'Nivel 1')
                ->setCellValue('F1', 'Cod 2')
                ->setCellValue('G1', 'Nivel 2')
                ->setCellValue('H1', 'Cod 3')
                ->setCellValue('I1', 'Nivel 3')
                ->setCellValue('J1', 'Cod 4')
                ->setCellValue('K1', 'Nivel 4');

        $objPHPExcel->getActiveSheet()->getStyle('A1:K1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objPHPExcel->getActiveSheet()->getStyle('A1:K1')->getFill()->getStartColor()->setARGB('FF808080');

        $data = $this->_proceson0->obtenerInventarioProcesos($this->_proyecto);

        //Generar nuevo array
        $contador = 0;
        $contadorNombre = 1;
        $nivel1 = 1;
        $nivel2 = 1;
        $nivel3 = 1;
        $nivel4 = 1;
        $arrayNombreN0 = array();
        $arrayNombreN1 = array();
        $arrayNombreN2 = array();
        $arrayNombreN3 = array();
        $arrayNombreN4 = array();
        foreach ($data as $value) {

            if ($contador == 0) { //La primera vez
                $data[$contador]['cod0'] = $contadorNombre;
                $arrayNombreN0[] = $value['nivel0'];
                $contadorNombre++;
            } else { //Cuando es mayor al contador 0
                //Extrae índice
                $indice = array_search($value['nivel0'], $arrayNombreN0);
                if (empty($indice)) { //No existe se agrega nuevo
                    $data[$contador]['cod0'] = $contadorNombre;
                    $arrayNombreN0[] = $value['nivel0'];
                    $contadorNombre++;
                    $nivel1 = 1;
                    $arrayNombreN1[] = array();
                } else { //Si existe
                    $data[$contador]['cod0'] = $indice + 1;
                }
            }

            if (empty($value['nivel1'])) {
                $data[$contador]['cod1'] = '';
            } else {
                $indice1 = array_search($value['nivel1'], $arrayNombreN1);
                if (empty($indice1)) { //Se agrega nuevo
                    $arrayNombreN1[] = $value['nivel1'];
                    $data[$contador]['cod1'] = $data[$contador]['cod0'] . "." . $nivel1;
                    $nivel1++;
                    $arrayNombreN2[] = array();
                    $nivel2 = 1;
                } else {
                    $n = $nivel1 - 1;
                    $data[$contador]['cod1'] = $data[$contador]['cod0'] . "." . $n;
                }
            }

            if (empty($value['nivel2'])) {
                $data[$contador]['cod2'] = '';
            } else {
                $indice2 = array_search($value['nivel2'], $arrayNombreN2);
                if (empty($indice2)) { //Se agrega nuevo
                    $arrayNombreN2[] = $value['nivel2'];
                    $data[$contador]['cod2'] = $data[$contador]['cod1'] . "." . $nivel2;
                    $nivel2++;
                    $arrayNombreN3[] = array();
                    $nivel3 = 1;
                } else {
                    $n = $nivel2 - 1;
                    $data[$contador]['cod2'] = $data[$contador]['cod1'] . "." . $n;
                }
            }

            if (empty($value['nivel3'])) {
                $data[$contador]['cod3'] = '';
            } else {
                $indice3 = array_search($value['nivel3'], $arrayNombreN3);
                if (empty($indice3)) { //Se agrega nuevo
                    $arrayNombreN3[] = $value['nivel3'];
                    $data[$contador]['cod3'] = $data[$contador]['cod2'] . "." . $nivel3;
                    $nivel3++;
                    $arrayNombreN4[] = array();
                    $nivel4 = 1;
                } else {
                    $n = $nivel3 - 1;
                    $data[$contador]['cod3'] = $data[$contador]['cod2'] . "." . $n;
                }
            }

            if (empty($value['nivel4'])) {
                $data[$contador]['cod4'] = '';
            } else {
                $indice4 = array_search($value['nivel4'], $arrayNombreN4);
                if (empty($indice4)) { //Se agrega nuevo
                    $arrayNombreN4[] = $value['nivel4'];
                    $data[$contador]['cod4'] = $data[$contador]['cod3'] . "." . $nivel4;
                    $nivel4++;
                } else {
                    $n = $nivel4 - 1;
                    $data[$contador]['cod4'] = $data[$contador]['cod3'] . "." . $n;
                }
            }

            $contador++;
        }

        $finalData = array();
        foreach ($data AS $row) {
            $finalData[] = array(
                $row["tipoproceso"],
                $row['cod0'],
                $row["nivel0"],
                $row['cod1'],
                $row["nivel1"],
                $row['cod2'],
                $row["nivel2"],
                $row['cod3'],
                $row["nivel3"],
                $row['cod4'],
                $row["nivel4"]
            );
        }

        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => 'FF000000'),
                )
            ),
        );

        $objPHPExcel->getActiveSheet()->fromArray($finalData, NULL, 'A2');
        $nReg = count($finalData) + 1;

        $objPHPExcel->getActiveSheet()->getStyle('A1:K' . $nReg)->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('A1:K1')->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(0);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Inventario-Procesos.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function matrizDotacionAction() {

        $objPHPExcel = new PHPExcel();

        $objPHPExcel->getProperties()->setCreator('Xperta Gestión Empresarial')
                ->setTitle('PHPExcel Test Document')
                ->setSubject('PHPExcel Test Document')
                ->setDescription('Matriz de Dimensionamiento')
                ->setKeywords('office PHPExcel php')
                ->setCategory('Test result file');
        $objPHPExcel->getActiveSheet()->setTitle('MatrizDotación');
        $objPHPExcel->setActiveSheetIndex(0);

        //$objPHPExcel->getActiveSheet()->getRowDimension('7')->setRowHeight(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(8); //->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(8);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(8);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(8);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(8);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(8);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(8);
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(13);
        $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('AB')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('AD')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('AE')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('AF')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('AG')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('AH')->setWidth(20);

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Tipo proceso')
                ->setCellValue('B1', 'Cod 0')
                ->setCellValue('C1', 'Nivel 0')
                ->setCellValue('D1', 'Cod 1')
                ->setCellValue('E1', 'Nivel 1')
                ->setCellValue('F1', 'Cod 2')
                ->setCellValue('G1', 'Nivel 2')
                ->setCellValue('H1', 'Cod 3')
                ->setCellValue('I1', 'Nivel 3')
                ->setCellValue('J1', 'Cod 4')
                ->setCellValue('K1', 'Nivel 4')
                ->setCellValue('L1', 'Cod Act')
                ->setCellValue('M1', 'Actividad')
                ->setCellValue('N1', 'Cod Tarea')
                ->setCellValue('O1', 'Tarea')
                ->setCellValue('P1', 'Número Correlativo')
                ->setCellValue('Q1', 'Ejecutor')
                ->setCellValue('R1', 'Naturaleza del Órgano')
                ->setCellValue('S1', 'Órgano')
                ->setCellValue('T1', 'Unidad Orgánica')
                ->setCellValue('U1', 'Periodicidad')
                ->setCellValue('V1', 'Frecuencia')
                ->setCellValue('W1', 'Frecuencia Mensual')
                ->setCellValue('X1', 'Duración (horas)')
                ->setCellValue('Y1', 'Tiempo Suplementario')
                ->setCellValue('Z1', 'Total Tiempo Mensual (horas)')
                ->setCellValue('AA1', 'Horas por Trabajador')
                ->setCellValue('AB1', 'Servidores Públicos')
                ->setCellValue('AC1', 'Grupo')
                ->setCellValue('AD1', 'Familia')
                ->setCellValue('AE1', 'Rol')
                ->setCellValue('AF1', 'Nivel')
                ->setCellValue('AG1', 'Categoría')
                ->setCellValue('AH1', 'Nombre puesto');

        $objPHPExcel->getActiveSheet()->getStyle('A1:AH1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objPHPExcel->getActiveSheet()->getStyle('A1:AH1')->getFill()->getStartColor()->setARGB('FF808080');

        $data = $this->_proceson0->obtenerMatrizDimensionamiento($this->_proyecto);

        //Generar nuevo array
        $contador = 0;
        $contadorNombre = 1;
        $nivel1 = 1;
        $nivel2 = 1;
        $nivel3 = 1;
        $nivel4 = 1;
        $arrayNombreN0 = array();
        $arrayNombreN1 = array();
        $arrayNombreN2 = array();
        $arrayNombreN3 = array();
        $arrayNombreN4 = array();
        foreach ($data as $value) {

            if ($contador == 0) { //La primera vez
                $data[$contador]['cod0'] = $contadorNombre;
                $arrayNombreN0[] = $value['nivel0'];
                $contadorNombre++;
            } else { //Cuando es mayor al contador 0
                //Extrae índice
                $indice = array_search($value['nivel0'], $arrayNombreN0);
                if (empty($indice)) { //No existe se agrega nuevo
                    $data[$contador]['cod0'] = $contadorNombre;
                    $arrayNombreN0[] = $value['nivel0'];
                    $contadorNombre++;
                    $nivel1 = 1;
                    $arrayNombreN1[] = array();
                } else { //Si existe
                    $data[$contador]['cod0'] = $indice + 1;
                }
            }

            if (empty($value['nivel1'])) {
                $data[$contador]['cod1'] = '';
            } else {
                $indice1 = array_search($value['nivel1'], $arrayNombreN1);
                if (empty($indice1)) { //Se agrega nuevo
                    $arrayNombreN1[] = $value['nivel1'];
                    $data[$contador]['cod1'] = $data[$contador]['cod0'] . "." . $nivel1;
                    $nivel1++;
                    $arrayNombreN2[] = array();
                    $nivel2 = 1;
                } else {
                    $n = $nivel1 - 1;
                    $data[$contador]['cod1'] = $data[$contador]['cod0'] . "." . $n;
                }
            }

            if (empty($value['nivel2'])) {
                $data[$contador]['cod2'] = '';
            } else {
                $indice2 = array_search($value['nivel2'], $arrayNombreN2);
                if (empty($indice2)) { //Se agrega nuevo
                    $arrayNombreN2[] = $value['nivel2'];
                    $data[$contador]['cod2'] = $data[$contador]['cod1'] . "." . $nivel2;
                    $nivel2++;
                    $arrayNombreN3[] = array();
                    $nivel3 = 1;
                } else {
                    $n = $nivel2 - 1;
                    $data[$contador]['cod2'] = $data[$contador]['cod1'] . "." . $n;
                }
            }

            if (empty($value['nivel3'])) {
                $data[$contador]['cod3'] = '';
            } else {
                $indice3 = array_search($value['nivel3'], $arrayNombreN3);
                if (empty($indice3)) { //Se agrega nuevo
                    $arrayNombreN3[] = $value['nivel3'];
                    $data[$contador]['cod3'] = $data[$contador]['cod2'] . "." . $nivel3;
                    $nivel3++;
                    $arrayNombreN4[] = array();
                    $nivel4 = 1;
                } else {
                    $n = $nivel3 - 1;
                    $data[$contador]['cod3'] = $data[$contador]['cod2'] . "." . $n;
                }
            }

            if (empty($value['nivel4'])) {
                $data[$contador]['cod4'] = '';
            } else {
                $indice4 = array_search($value['nivel4'], $arrayNombreN4);
                if (empty($indice4)) { //Se agrega nuevo
                    $arrayNombreN4[] = $value['nivel4'];
                    $data[$contador]['cod4'] = $data[$contador]['cod3'] . "." . $nivel4;
                    $nivel4++;
                } else {
                    $n = $nivel4 - 1;
                    $data[$contador]['cod4'] = $data[$contador]['cod3'] . "." . $n;
                }
            }

            $contador++;
        }

        $finalData = array();
        foreach ($data AS $row) {
            $finalData[] = array(
                $row["tipoproceso"],
                $row['cod0'],
                $row["nivel0"],
                $row['cod1'],
                $row["nivel1"],
                $row['cod2'],
                $row["nivel2"],
                $row['cod3'],
                $row["nivel3"],
                $row['cod4'],
                $row["nivel4"],
                $row['num_act'],
                $row["actividad"],
                $row["num_tarea"],
                $row["tarea"],
                $row["num_puesto"],
                $row["puesto"],
                $row["natu_orga"],
                $row["organo"],
                $row["unidad_organica"],
                $row["periodicidad"],
                $row["frecuencia"],
                $row["frecuencia_mensual"],
                $row["duracion_horas"],
                $row["tiempo_suple"],
                $row["total_tiempo_mensual"],
                $row["horas_trabaja"],
                $row["servidores_publicos"],
                $row["grupo"],
                $row["familia"],
                $row["rol"],
                $row["nivel_puesto"],
                $row["categoria_puesto"],
                $row['nombre_puesto']
            );
        }

        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => 'FF000000'),
                )
            ),
        );

        $objPHPExcel->getActiveSheet()->fromArray($finalData, NULL, 'A2');
        $nReg = count($finalData) + 1;

        $objPHPExcel->getActiveSheet()->getStyle('A1:AH' . $nReg)->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('A1:AH1')->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(0);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Matriz-Dimensionamiento.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function resumenAction() {
        Zend_Layout::getMvcInstance()->assign('active', 'Resumen');
        Zend_Layout::getMvcInstance()->assign('padre', 8);
        Zend_Layout::getMvcInstance()->assign('link', 'resumen');
    }

    public function exportResumenAction() {

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator('Xperta Gestión Empresarial')
                ->setTitle('PHPExcel Test Document')
                ->setSubject('PHPExcel Test Document')
                ->setDescription('Mapeo de puestos')
                ->setKeywords('office PHPExcel php')
                ->setCategory('Test result file');
        $objPHPExcel->getActiveSheet()->setTitle('Resumen');
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30); //->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Órgano / Unidad Orgánica')
                ->setCellValue('B1', 'Cantidad de ocupados Situación Actual')
                ->setCellValue('C1', 'Cantidad de Servidores Públicos según Carga de Trabajo');
        $objPHPExcel->getActiveSheet()->getStyle('A1:C1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objPHPExcel->getActiveSheet()->getStyle('A1:C1')->getFill()->getStartColor()->setARGB('FF808080');

        $data = $this->_puesto->obtenerPuestosProyecto($this->_proyecto);
        //Generar nuevo array
        $contador = 0;
        $nombreUnidad = array();
        $dataResumen = array();
        $sumaActual = 0;
        $sumaDotacion = 0;
        $indicaUnidad = 0;

        $totalActual = 0;
        $totalDotacion = 0;

        foreach ($data as $value) {

            if (!in_array($value['unidad'], $nombreUnidad)) {
                $dataResumen[$contador]['puesto'] = $value['unidad'];
                $dataResumen[$contador]['cantidad'] = '';
                $dataResumen[$contador]['unidad'] = '';
                $dataResumen[$contador]['total_dotacion'] = '0.00';
                $dataResumen[$indicaUnidad]['cantidad'] = $sumaActual;
                $dataResumen[$indicaUnidad]['total_dotacion'] = $sumaDotacion;
                $totalActual += $sumaActual;
                $totalDotacion += $sumaDotacion;
                $indicaUnidad = $contador;
                $contador++;
                $dataResumen[$contador]['puesto'] = $value['puesto'];
                $dataResumen[$contador]['cantidad'] = $value['cantidad'];
                $dataResumen[$contador]['unidad'] = $value['unidad'];
                $dataResumen[$contador]['total_dotacion'] = $value['total_dotacion'];
                $nombreUnidad[] = $value['unidad'];
                $sumaActual = 0;
                $sumaActual += $value['cantidad'];
                $sumaDotacion = 0;

                $redondeo = round($value["total_dotacion"], 0);
                if ($value['total_dotacion'] < 0.54) {
                    $redondeo = 0;
                }
                $sumaDotacion += $redondeo;
            } else { //Si existe
                $dataResumen[$contador]['puesto'] = $value['puesto'];
                $dataResumen[$contador]['cantidad'] = $value['cantidad'];
                $dataResumen[$contador]['unidad'] = $value['unidad'];
                $dataResumen[$contador]['total_dotacion'] = $value['total_dotacion'];
                $sumaActual += $value['cantidad'];
                $redondeo = round($value["total_dotacion"], 0);
                if ($value['total_dotacion'] < 0.54) {
                    $redondeo = 0;
                }
                $sumaDotacion += $redondeo;
            }

            $contador++;
        }

        $dataResumen[$indicaUnidad]['cantidad'] = $sumaActual;
        $dataResumen[$indicaUnidad]['total_dotacion'] = $sumaDotacion;

        //Totales
        $dataResumen[$contador]['puesto'] = 'Total General';
        $dataResumen[$contador]['cantidad'] = $totalActual + $sumaActual;
        $dataResumen[$contador]['total_dotacion'] = $totalDotacion + $sumaDotacion;

        $finalData = array();
        foreach ($dataResumen AS $row) {

            $redondeo = round($row["total_dotacion"], 0);
            if ($row['total_dotacion'] < 0.54) {
                $redondeo = "0";
            }

            $finalData[] = array(
                $row["puesto"],
                $row['cantidad'],
                $redondeo
            );
        }

        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => 'FF000000'),
                )
            ),
        );

        $objPHPExcel->getActiveSheet()->fromArray($finalData, NULL, 'A2');
        $nReg = count($finalData) + 1;

        $objPHPExcel->getActiveSheet()->getStyle('A1:C' . $nReg)->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('A1:C1')->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(0);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Reporte-Resumen.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function obtenerPuestosDotacionAction() {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $data = $this->_getAllParams();
        //Previene vulnerabilidad XSS (Cross-site scripting)
        $filtro = new Zend_Filter_StripTags();
        foreach ($data as $key => $val) {
            $data[$key] = $filtro->filter(trim($val));
        }

        if (!$this->getRequest()->isXmlHttpRequest())
            exit('Acción solo válida para peticiones ajax');

        $unidad = $data['unidad'];
        $dataPuesto = $this->_puesto->obtenerPuestoDotacion($unidad);
        echo Zend_Json::encode($dataPuesto);
    }

    public function puestosNaturalezaAction() {
        Zend_Layout::getMvcInstance()->assign('active', 'Puestos por Naturaleza del Órgano');
        Zend_Layout::getMvcInstance()->assign('padre', 8);
        Zend_Layout::getMvcInstance()->assign('link', 'puestos_naturaleza');

        $this->view->headScript()->appendFile(SITE_URL . '/js/reportes/puestos-naturaleza.js');
        $this->view->organo = $this->_organo->obtenerOrgano($this->_proyecto);
    }

    public function exportExcelPuestosNaturalezaAction() {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator('Xperta Gestión Empresarial')
                ->setTitle('PHPExcel Test Document')
                ->setSubject('PHPExcel Test Document')
                ->setDescription('Mapeo de puestos')
                ->setKeywords('office PHPExcel php')
                ->setCategory('Test result file');
        $objPHPExcel->getActiveSheet()->setTitle('Puestos_Naturaleza');
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30); //->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);

        $nomOrgano = $this->_getParam('nomorgano');
        $nomUnidad = $this->_getParam('nomunidad');
        
        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Órgano')
                ->setCellValue('B1', $nomOrgano)
                ->setCellValue('C1', 'Unidad Orgánica')
                ->setCellValue('D1', $nomUnidad);
        
        $objPHPExcel->getActiveSheet()->setCellValue('A2', 'N')
                ->setCellValue('B2', 'Naturaleza del Órgano')
                ->setCellValue('C2', 'Nombre del puesto')
                ->setCellValue('D2', 'Posiciones');
        $objPHPExcel->getActiveSheet()->getStyle('A2:D2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objPHPExcel->getActiveSheet()->getStyle('A2:D2')->getFill()->getStartColor()->setARGB('FF808080');

        $unidad = $this->_getParam('unidad');
        $data = $this->_puesto->obtenerPuestos($unidad);
        //Generar nuevo array
        $contador = 0;
        $totalCantidad = 0;
        $dataResumen = array();

        foreach ($data as $value) {
            $contador++;
            $dataResumen[$contador]['num'] = $contador;
            $dataResumen[$contador]['puesto'] = $value['puesto'];
            $dataResumen[$contador]['cantidad'] = $value['cantidad'];
            $dataResumen[$contador]['naturaleza'] = $value['naturaleza'];
            $totalCantidad += $value['cantidad'];
        }
        
        $contador++;

        $dataResumen[$contador]['num'] = '';
        $dataResumen[$contador]['puesto'] = 'Total General';
        $dataResumen[$contador]['cantidad'] = $totalCantidad;
        $dataResumen[$contador]['naturaleza'] = '';

        $finalData = array();
        foreach ($dataResumen AS $row) {
            $finalData[] = array(
                $row["num"],
                $row["naturaleza"],
                $row["puesto"],
                $row['cantidad']
            );
        }

        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => 'FF000000'),
                )
            ),
        );

        $objPHPExcel->getActiveSheet()->fromArray($finalData, NULL, 'A3');
        $nReg = count($finalData) + 1;

        $objPHPExcel->getActiveSheet()->getStyle('A1:D' . $nReg)->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('A1:D1')->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(0);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Puestos-Naturaleza.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save("Puestos-Naturaleza.xlsx");

        echo Zend_Json::encode(array("success" => 1));
    }

}
