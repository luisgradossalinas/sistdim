<?php

class App_Controller_Action_Helper_Word extends Zend_Controller_Action_Helper_Abstract {


    public function repOrganoUnidad($data, $organo, $unidad) {

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

        $section->addText(utf8_decode("Órgano: ".$organo."   Unidad Orgánica: ".$unidad));
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
        $nreg = count($data);
        foreach ($data as $value) {
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

    }

}
