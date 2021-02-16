<?php
include_once("check_login.php");
include_once('class/common_settings.php');
include_once("class/Crud.php");
include_once("class/Report.php");

$crud = new Crud();
if(isset($_POST["query"]))  
{  
     $output = '';  
     $branch = $_POST["branch"];
     $query = "SELECT * FROM product_detail WHERE Barcode LIKE '%".$_POST["query"]."%' and Branch_Id = $branch";  
     $result =  $crud->getData($query);
     $output = '<ul class="list-unstyled auto_list">';  
     if($crud->number_of_records($query) > 0)  
     {  
        foreach($result as $row)
          {
               $output .= '<li>'.$row["Barcode"].'</li>';  
          }  
     }  
     else  
     {  
          $output .= '<li>Barcode Not Found</li>';  
     }  
     $output .= '</ul>';  
     echo $output;  
}  
?>