<?php
include_once("check_login.php");
include_once('class/common_settings.php');
include_once("class/Crud.php");
include_once("class/Report.php");

$crud = new Crud();

if(isset($_POST["id"])){
	
 $value = $crud->escape_string($_POST["value"]);
 $query = "UPDATE sales_master SET ".$_POST["column_name"]."='".$value."' WHERE Sales_Master_Id = '".$_POST["id"]."'";
 $crud->execute($query);
 $data = "Data Updated";
 echo $data;
 if($crud->execute($query))
 {
 // $data = "Data Updated";
  echo $data;
 }
}

?>