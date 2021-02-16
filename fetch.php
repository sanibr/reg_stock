<?php
include_once("check_login.php");
include_once('class/common_settings.php');
include_once("class/Crud.php");
include_once("class/Report.php");

$crud = new Crud();

$column = array('Invoice_Date', 'Invoice_No', 'Total_Gross_Amount', 'Total_Discount_Amount', 'Total_Net_Amount');

$query = "SELECT * FROM sales_master ";

$count_all_data = $crud->number_of_records($query);

if(isset($_POST['filter_voucher_no'], $_POST['filter_transaction_date']) && $_POST['filter_voucher_no'] != '' && $_POST['filter_transaction_date'] != '')
{
     $query .= 'WHERE Invoice_No = "'.$_POST['filter_voucher_no'].'" AND Invoice_Date = "'.$crud->format_date($_POST['filter_transaction_date']).'"';
}

if(isset($_POST['order']))
{
     $query .= 'ORDER BY '.$column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
}
else
{
     $query .= 'ORDER BY Sales_Master_Id DESC ';
}

$query1 = '';

if($_POST["length"] != -1)
{
$query1 = 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
}

$statement = $crud->getData($query);
$number_filter_row = $crud->number_of_records($query);
$result    = $crud->getData($query.$query1);

$data = array();
foreach($result as $row)
{
$sub_array = array();
$sub_array[] = '<div contenteditable class="update" data-id="'.$row["Sales_Master_Id"].'" data-column="Invoice_Date">' . $row["Invoice_Date"] . '</div>';
$sub_array[] = '<div contenteditable class="update" data-id="'.$row["Sales_Master_Id"].'" data-column="Invoice_No">' . $row["Invoice_No"] . '</div>';
$sub_array[] = '<div contenteditable class="update" data-id="'.$row["Sales_Master_Id"].'" data-column="Total_Gross_Amount">' . $row["Total_Gross_Amount"] . '</div>';
$sub_array[] = '<div contenteditable class="update" data-id="'.$row["Sales_Master_Id"].'" data-column="Total_Discount_Amount">' . $row["Total_Discount_Amount"] . '</div>';
$sub_array[] = '<div contenteditable class="update" data-id="'.$row["Sales_Master_Id"].'" data-column="Total_Net_Amount">' . $row["Total_Net_Amount"] . '</div>';
$sub_array[] = '<button type="button" name="delete" class="btn btn-danger btn-xs delete" id="'.$row["Sales_Master_Id"].'">Delete</button>';
$data[] = $sub_array;
}

$output = array(
    "draw"       =>  intval($_POST["draw"]),
    "recordsTotal"   =>  $count_all_data,
    "recordsFiltered"  =>  $number_filter_row,
    "data"       =>  $data
   );


   //print_r($output);

  
   
   echo json_encode($output);
?>