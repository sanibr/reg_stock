<?php
include_once("check_login.php");
include_once('class/common_settings.php');
include_once("class/Crud.php");
include_once("class/Report.php");

$crud = new Crud();
$sal_id       = $_POST['sal_id'];

$sql="SELECT sd.Sales_Detail_Id,sd.Sales_Master_Id,sd.Product_Id,pm.Product_name,pm.Product_Code,pb.Brand_Name,pc.Category_Name,sd.Quantity,sd.Sales_Rate,sd.Gross_Amount,sd.Discount,sd.Net_Amount,sd.Tax_Amount,sd.Amount FROM sales_detail sd 
	left join product_master pm on sd.Product_Id=pm.Product_Id
	left join product_brand pb on pm.Brand_ID=pb.Brand_ID
	left join product_category pc on pm.Category_ID=pc.Category_ID
	where sd.Sales_Master_Id = ".$sal_id."  order by sd.Sales_Detail_Id desc ";
	
$sales_detail_list = $crud->getData($sql);

    echo '
    <thead>
    <tr class="stbl2_th">
    <th>Product Code</th>
    <th>Product Name</th>
    <th>Brand Name</th>
    <th>Quantity</th>
    <th>Rate</th>
    <th>Gross Amount</th>
    <th>Discount Amount</th>
    <th>Net Amount</th>
    <th>Tax Amount</th>
    <th>Total Amount</th></tr></thead>
    <tbody>';
    foreach ($sales_detail_list as $k => $sales_detail) {
       $sales_detail['Sales_Rate'] = $crud->num_format($sales_detail['Sales_Rate']);
       $sales_detail['Gross_Amount'] = $crud->num_format($sales_detail['Gross_Amount']);
       $sales_detail['Discount'] = $crud->num_format($sales_detail['Discount']);
       $sales_detail['Net_Amount'] = $crud->num_format($sales_detail['Net_Amount']);
       $sales_detail['Tax_Amount'] = $crud->num_format($sales_detail['Tax_Amount']);
       $sales_detail['Amount'] = $crud->num_format($sales_detail['Amount']);

    echo  '<tr class="stbl2 sd_'.$sal_id.'">
     <td>'.$sales_detail['Product_Code'].'</td>
     <td>'.$sales_detail['Product_name'].'</td>
     <td>'.$sales_detail['Brand_Name'].'</td>
     <td>'.$sales_detail['Quantity'].'</td>
     <td>'.$sales_detail['Sales_Rate'].'</td>
     <td>'.$sales_detail['Gross_Amount'].'</td>
     <td>'.$sales_detail['Discount'].'</td>
     <td>'.$sales_detail['Net_Amount'].'</td>
     <td>'.$sales_detail['Tax_Amount'].'</td>
     <td>'.$sales_detail['Amount'].'</td></tr>';
     }
    
  echo '</tbody>';
  ?>
<style>
.stbl_td > td{
  background: #cae1c5!important;
  border-right: 1px solid #f1f1f1!important;
} 
</style>

