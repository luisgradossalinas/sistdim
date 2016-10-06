<?php
require_once '../PHPWord.php';

// New Word Document
$PHPWord = new PHPWord();

// New portrait section
$section = $PHPWord->createSection();

//Template-Organo-Unidad.docx
//$section = $PHPWord->loadTemplate('Template-Organo-Unidad.docx');

// Define table style arrays
$styleTable = array('borderSize'=> 6, 'borderColor'=>'000000', 'cellMargin'=>80);
$styleFirstRow = array('borderBottomSize'=>18, 'borderBottomColor'=>'000000', 'bgColor'=>'BFD7FA');

// Define cell style arrays
$styleCell = array('valign'=>'center');
$styleCellBTLR = array('valign'=>'center', 'textDirection'=>PHPWord_Style_Cell::TEXT_DIR_BTLR); //Texto en vertical

// Define font style for first row
$fontStyle = array('bold'=>true, 'align'=>'center');


$section->addText(utf8_encode("Organo : Despacho Ministerial    Unidad Organica: Despacho Ministerial"));
//$section->addTextBreak(1); // Enter

// Add table style
$PHPWord->addTableStyle('myOwnTableStyle', $styleTable, $styleFirstRow);

// Add table
$table = $section->addTable('myOwnTableStyle');

$textoCenter = array('align'=>'center');
// Add row
$table->addRow(900);

// Add cells
$table->addCell(200, $styleCell)->addText('N', $fontStyle);
$table->addCell(3000, $styleCell)->addText('Ejecutor', $fontStyle);
$table->addCell(2000, $styleCell)->addText('Suma Dotacion Actual', $fontStyle);
$table->addCell(2000, $styleCell)->addText('Suma segun Carga de Trabajo', $fontStyle);
$table->addCell(2000, $styleCell)->addText('Suma de Necesidades de Dotacion', $fontStyle);


// Add more rows / cells
for($i = 1; $i <= 15; $i++) {
	$table->addRow();
	$table->addCell(200)->addText($i);
	$table->addCell(3000)->addText("Cell $i"); //Ejecutor
	$table->addCell(2000,$styleCell)->addText("Cell $i",$textoCenter); //Suma dotación atual X
	$table->addCell(2000,$styleCell)->addText("Cell $i",$textoCenter); //Suma carga de trabajo Y
	$table->addCell(2000,$styleCell)->addText("Cell $i",$textoCenter); //Y-X
}

$table->addRow();
	$table->addCell(200)->addText('');
	$table->addCell(3000)->addText('');
	$table->addCell(2000)->addText('');
	$table->addCell(2000)->addText('Total');
	$table->addCell(2000,$styleCell)->addText(4,$textoCenter);

$filename = 'Organo-Unidad.docx';
// Save File
$objWriter = PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
$objWriter->save($filename);



header("Content-Type: application/vnd.ms-word; charset=utf-8");
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename='.$filename);
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Length: ' . filesize($filename));
flush();
readfile($filename);
unlink($filename); // deletes the temporary file
exit;


?>