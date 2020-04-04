<?php

/** PHPExcel_IOFactory */
require_once dirname(__FILE__) . '/PHPExcel/Classes/PHPExcel/IOFactory.php';

$rendererName = PHPExcel_Settings::PDF_RENDERER_DOMPDF;
$rendererLibrary = 'dompdf/dompdf';
$rendererLibraryPath = dirname(__FILE__).'/'. $rendererLibrary;


if (!PHPExcel_Settings::setPdfRenderer(
    $rendererName,
    $rendererLibraryPath
)) {
die(
    'NOTICE: Please set the $rendererName and $rendererLibraryPath values' .
    EOL .
    'at the top of this script as appropriate for your directory structure'
);
}

?>