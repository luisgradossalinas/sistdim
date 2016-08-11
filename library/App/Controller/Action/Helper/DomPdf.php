<?php
/*
 * Helper para manejor de DomPdf.
 * 
 */
class App_Controller_Action_Helper_DomPdf
    extends Zend_Controller_Action_Helper_Abstract
{
    public function render($html, $tamano, $orientacion='portrait',$file=false) {
        $dompdf = new DOMPDF();
        if ($file) {
            $dompdf->load_html_file($html);
        } else {
            $dompdf->load_html($html);
        }
        $dompdf->set_paper($tamano, $orientacion);
        $dompdf->set_base_path($_SERVER['DOCUMENT_ROOT']);
        $dompdf->render();
        return $dompdf;
    }
    public function descargarPDF($html, $tamano, $orientacion='portrait',$filename) {
        $dompdf = $this->render($html, $tamano, $orientacion);
        $dompdf->stream($filename, array("Attachment" => 1));
    }
    public function mostrarPDF($html, $tamano, $orientacion='portrait',$filename) {
        $dompdf = $this->render($html, $tamano, $orientacion);
        $dompdf->stream($filename, array("Attachment" => 0));
    }
    public function guardarPDF($html, $tamano, $orientacion='portrait',$filename) {
        $dompdf = $this->render($html, $tamano, $orientacion);
        $pdf = $dompdf->output();
        file_put_contents($filename, $pdf);
    }
    /*
     * Para convertir una URL a PDF
     */
    public function descargarPDF_URL($url, $tamano, $orientacion='portrait',$filename) {
        $dompdf = $this->render($url, $tamano, $orientacion, true);
        $dompdf->stream($filename, array("Attachment" => 1));
    }
    public function mostrarPDF_URL($url, $tamano, $orientacion='portrait',$filename) {
        $dompdf = $this->render($url, $tamano, $orientacion, true);
        $dompdf->stream($filename, array("Attachment" => 0));
    }
    public function guardarPDF_URL($url, $tamano, $orientacion='portrait',$filename) {
        $dompdf = $this->render($url, $tamano, $orientacion, true);
        $pdf = $dompdf->output();
        file_put_contents($filename, $pdf);
    }
}