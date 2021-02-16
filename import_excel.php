<?php
include_once("check_login.php");
include_once('class/common_settings.php');
include_once("class/Crud.php");
include_once("class/Report.php");

$crud = new Crud();

require "vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if(isset($_POST['import_file_btn']))
{
  $allowed_ext = ['xls','csv','xlsx'];
  $fileName = $_FILES['import_file']['name'];
  $checking = explode('.',$fileName);
  $file_ext = end($checking);
  if(in_array($file_ext,$allowed_ext)){
    $targetPath = $_FILES['import_file']['tmp_name'];
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($targetPath);
    $data = $spreadsheet->getActiveSheet()->toArray();
    foreach(array_slice($data,1) as $row){
      $product_code = $row['0'];
      $quantity = $row['1'];
    }

  } 
  else
  {
    $_SESSION['status'] = "Invalid Files";
    header("Location:stock_transfer.php");
    exit(0);
  }



}

?>