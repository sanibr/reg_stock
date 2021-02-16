<?php
require "vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
 
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Basic');
$sheet->setCellValue('A1', 'Product Code');
$sheet->setCellValue('B1', 'Quantity');

$date = date('d-m-y-');
$date = str_replace(".", "", $date);
$filename = "TransferExcel".$date.".xlsx";

try {
    //$writer = new Xlsx($response["spreadsheet"]);
    $writer = new Xlsx($spreadsheet);
    $writer->save($filename);
    $content = file_get_contents($filename);
} catch(Exception $e) {
    exit($e->getMessage());
}

header("Content-Disposition: attachment; filename=".$filename);

unlink($filename);
exit($content);
 ?>