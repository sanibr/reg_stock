<?php
include_once("check_login.php");
include_once('class/common_settings.php');
include_once("class/Crud.php");
include_once("class/Report.php");

$crud = new Crud();
$s_id       = $_POST['s_id'];
$s_inv      = $_POST['s_inv'];
$where      = $_POST['where']; 

$sql_det="SELECT sm.Sales_Master_Id,sm.Serial_No,sm.Invoice_Date,sm.Invoice_No,sm.Customer_Name,sm.IsCash,CASE
WHEN sm.IsCash = 0 THEN 'Cash'
WHEN sm.IsCash = 1 THEN 'Card'
WHEN sm.IsCash = 2 THEN 'Credit'
WHEN sm.IsCash = 3 THEN 'Multi'
ELSE 'Cash'
END as paymentmode,sm.Total_Gross_Amount,sm.Total_Discount_Amount,sm.Total_Net_Amount,sm.Total_Tax_Amount,sm.Total_Amount 
FROM sales_master sm 
left join customer sc on sm.Customer_Id=sc.Customer_Id where sm.Invoice_Date = '".$s_inv."' and is_saved='true'
$where order by sm.Sales_Master_Id desc ";

$sales_det_list = $crud->getData($sql_det);

	echo '
	<thead>
	<tr class="stbl_th">
	<th>Invoice Date</th>
    <th>Invoice No</th>
	<th>Customer Name</th>
	<th>Payment Mode</th>
	<th>Gross Amount</th>
	<th>Discount</th>
	<th>Net Amount</th>
	<th>Tax Amount</th>
	<th>Total Amount</th></tr></thead>
	<tbody>';
	foreach ($sales_det_list as $k => $sales_detl) {
	  $sales_detl['Sales_Rate'] = $crud->num_format($sales_detl['Sales_Rate']);
	  $sales_detl['Gross_Amount'] = $crud->num_format($sales_detl['Gross_Amount']);
	  $sales_detl['Discount'] = $crud->num_format($sales_detl['Discount']);
	  $sales_detl['Net_Amount'] = $crud->num_format($sales_detl['Net_Amount']);
	  $sales_detl['Tax_Amount'] = $crud->num_format($sales_detl['Tax_Amount']);
	  $sales_detl['Amount'] = $crud->num_format($sales_detl['Amount']);

   echo  '<tr data-sales_id="'.$sales_detl['Sales_Master_Id'].'" class="stbl_td ff fd_'.$s_id.'">
   <td>'.$crud->disp_date($sales_detl['Invoice_Date']).'</td>
   <td>'.$sales_detl['Invoice_No'].'</td>
   <td>'.$sales_detl['Customer_Name'].'</td>
   <td>'.$sales_detl['paymentmode'].'</td>
   <td>'.$sales_detl['Total_Gross_Amount'].'</td>
   <td>'.$sales_detl['Total_Discount_Amount'].'</td>
   <td>'.$sales_detl['Total_Net_Amount'].'</td>
   <td>'.$sales_detl['Total_Tax_Amount'].'</td>
   <td>'.$sales_detl['Total_Amount'].'</td></tr>';

	$sql="SELECT sd.Sales_Detail_Id,sd.Sales_Master_Id,sd.Product_Id,pm.Product_name,pm.Product_Code,pb.Brand_Name,pc.Category_Name,sd.Quantity,sd.Sales_Rate,sd.Gross_Amount,sd.Discount,sd.Net_Amount,sd.Tax_Amount,sd.Amount FROM sales_detail sd 
	left join product_master pm on sd.Product_Id=pm.Product_Id
	left join product_brand pb on pm.Brand_ID=pb.Brand_ID
	left join product_category pc on pm.Category_ID=pc.Category_ID
	where sd.Sales_Master_Id = ".$sales_detl['Sales_Master_Id']."  order by sd.Sales_Detail_Id desc ";
	
    $sales_detail_list = $crud->getData($sql);
    
    if ($sales_detail_list){
        echo '<tr class="stbl sd_'.$s_id.'">
            <td colspan="10" class="sub_row_td">
            <table class="table b-t b-light tbl_mr" id="saldetail_list'.$sales_detl['Sales_Master_Id'].'">';
        echo '</table></td></tr/>';
    } else{
       echo  '<tr class="td_error sub_row hidden aa_'.$s_id.'">
              <td colspan="'.($cnt == 1?'10':'11').'">No Record Found</td></tr>';
    }
									
      }
      
	echo '</tbody>';
  ?>
<style>
.stbl_td > td{
  background: #cae1c5!important;
  border-right: 1px solid #f1f1f1!important;
} 
</style>
<script>
$( document ).ready(function() {
	$(".ff").on('click',function(){
    var sal_id = $(this).data('sales_id');
    $('.loader').show(); 
	$(this).closest('tr').next('tr').toggleClass("active");
    $.ajax({    //create an ajax request to display.php
		type: "POST",
		url: "sales_detail_list_view.php",             
		dataType: "html",   //expect html to be returned 
		data: {sal_id:sal_id},               
		success: function(response){                    
			$("#saldetail_list"+sal_id).html(response); 
            $('.loader').hide(); 
			//alert(response);
		}
     });
 });
});
</script>
