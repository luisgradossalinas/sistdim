<?php
$filename = 'test.doc';
header("Content-Type: application/vnd.ms-word; charset=utf-8");
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename='.$filename);
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Length: ' . filesize($filename));

echo "<html>";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">";
echo "<body>";
echo "<b>Mi primer documento</b><br />";
echo "Aqu&iacute; va todo el testo que querais, en formato HTML</body>";
echo "</html>";

?>
