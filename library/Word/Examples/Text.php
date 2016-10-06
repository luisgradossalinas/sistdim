<?php
require_once '../PHPWord.php';

// New Word Document
$PHPWord = new PHPWord();

// New portrait section
$section = $PHPWord->createSection();

// Add text elements
$section->addText('Hello World!');
$section->addTextBreak(2);

$section->addText('I am inline styled.', array('name'=>'Verdana', 'color'=>'006699'));
$section->addTextBreak(2);

$PHPWord->addFontStyle('rStyle', array('bold'=>true, 'italic'=>true, 'size'=>16));
$PHPWord->addParagraphStyle('pStyle', array('align'=>'center', 'spaceAfter'=>100));
$section->addText('I am styled by two style definitions.', 'rStyle', 'pStyle');
$section->addText('I have only a paragraph style definition.', null, 'pStyle');



// Save File
$objWriter = PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
$objWriter->save('Text.docx');

header("Content-Disposition: attachment; filename='Text.docx'");

/*
readfile($temp_file); // or echo file_get_contents($temp_file);
unlink($temp_file);  // remove temp file
*/

?>