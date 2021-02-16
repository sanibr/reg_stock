<?php
include_once("check_login.php");
include_once('class/common_settings.php');
include_once("class/Crud.php");
include_once("class/Report.php");

$crud = new Crud();

$where = "";

if ((isset($_GET['unset_filter']) && $_GET['unset_filter']==1 )|| empty($_GET['inv_from_date'])&&empty($_GET['inv_to_date'])) {
	$_GET['inv_from_date'] = date('d-m-Y');
	$_GET['inv_to_date'] = date('d-m-Y');
	$_GET['branch_id'] = "";
}

if (isset($_GET['inv_from_date']) && !empty($_GET['inv_from_date']) || isset($_GET['inv_to_date']) && !empty($_GET['inv_to_date']) ) {
    if($_SESSION['db_type'] == "mysql" || !isset($_SESSION['db_type'])){
			$where.=" and date(pm.Invoice_Date)  >='".$crud->format_date($_GET['inv_from_date'])."' and date(pm.Invoice_Date)  <='".$crud->format_date($_GET['inv_to_date'])."'";
    } else if ($_SESSION['db_type'] == "mssql"){
        	$where.=" and pm.Invoice_Date  >='".$crud->format_date($_GET['inv_from_date'])."' and pm.Invoice_Date  <='".$crud->format_date($_GET['inv_to_date'])."'";
    }
}

// if (isset($_GET['company_id']) && !empty($_GET['company_id'])) {
// 	$where.=" and pm.Company_Id = ".(int) $_GET['company_id'];
// }

// $company = $crud->getData("select Company_Id,Company_Name from company order by Company_Name");
// $company_count = $crud->getData("SELECT COUNT(DISTINCT company_id) AS com_count FROM purchase_master");
// $cnt = $company_count[0]['com_count'];


if (isset($_GET['branch_id']) && !empty($_GET['branch_id'])) {
	$where.=" and pm.Branch_Id = ".(int) $_GET['branch_id'];
}

$branch = $crud->getData("select Branch_Id,Branch_Name from branch order by Branch_Name");
$branch_count = $crud->getData("SELECT COUNT(DISTINCT branch_id) AS brn_count FROM purchase_master");
$cnt = $branch_count[0]['brn_count'];

$no_of_records_per_page = 10;

if (isset($_GET['pageno'])) {
	$pageno = $_GET['pageno'];
	$counter = ($pageno-1) * $no_of_records_per_page+1;
} else {
	$counter = 1;
	$pageno = 1;
}

$no_of_records_per_page = 10;
$offset = ($pageno-1) * $no_of_records_per_page; 

    if($_SESSION['db_type'] == "mysql" || !isset($_SESSION['db_type'])){
		
		$invdate = "DATE_FORMAT(pm.Invoice_Date,'%d/%m/%Y')";
		
    } else if ($_SESSION['db_type'] == "mssql"){
    
        $invdate = "FORMAT(pm.Invoice_Date,'dd-MM-yyyy')";
      
    }

$sql_rep="SELECT pm.Purchase_Master_Id,pm.Invoice_No,$invdate AS Invoice_Date,pm.Supplier_Name,pm.Is_Cash,CASE
WHEN pm.Is_Cash = 0 THEN 'Cash'
WHEN pm.Is_Cash = 1 THEN 'Card'
WHEN pm.Is_Cash = 2 THEN 'Credit'
WHEN pm.Is_Cash = 3 THEN 'Multi'
ELSE 'Cash'
END as paymentmode,pm.Total_Gross_Amount,pm.Total_Discount_Amount,pm.Total_Net_Amount,pm.Total_Tax_Amount,pm.Total_Amount,pm.Discount_Amount,pm.Grand_Total 
FROM purchase_master pm where 1=1 and is_saved='true'
$where order by pm.Purchase_Master_Id desc ";

$sql="SELECT pm.Company_Id,c.Company_Name,pm.Branch_Id,b.Branch_Name,$invdate as invoice_date,sum(pm.Total_Gross_Amount) as Total_Gross_Amount ,sum(pm.Total_Discount_Amount) as Total_Discount_Amount,sum(pm.Total_Net_Amount) as Total_Net_Amount,sum(pm.Total_Tax_Amount) as Total_Tax_Amount,sum(pm.Total_Amount) as Total_Amount,sum(pm.Discount_Amount) as Discount_Amount,sum(pm.Grand_Total) as Grand_Total 
FROM purchase_master pm 
left join company c on pm.Company_Id=c.Company_Id 
left join branch b on pm.Branch_Id=b.Branch_Id
where 1=1 and is_saved='true'
$where group by pm.Company_Id,c.Company_Name,pm.Branch_Id,b.Branch_Name,pm.Invoice_Date order by pm.Purchase_Master_Id desc ";

$total_rows = $crud->number_of_records($sql);
$total_pages = ceil($total_rows / $no_of_records_per_page);

 if($_SESSION['db_type'] == "mysql" || !isset($_SESSION['db_type'])){
       
	    $sql_data = "$sql LIMIT $offset, $no_of_records_per_page";
	    
    } else if ($_SESSION['db_type'] == "mssql"){
        
        
        $sql_data = "$sql OFFSET $offset ROWS FETCH NEXT $no_of_records_per_page ROWS ONLY ";
    }
    
$purchase_list = $crud->getData($sql_data);

$purchaselist_all = $crud->getData($sql_rep);
$sum_gross_amount = 0;
foreach ($purchaselist_all as $k => $purchase) {
	$sum_gross_amount += $purchase['Total_Gross_Amount'];
	$sum_total_discount_amount += $purchase['Total_Discount_Amount'];
	$sum_net_amount += $purchase['Total_Net_Amount'];
	$sum_tax_amount += $purchase['Total_Tax_Amount'];
	$sum_total_amount += $purchase['Total_Amount'];
	$sum_discount_amount += $purchase['Discount_Amount'];
	$sum_grand_total += $purchase['Grand_Total'];
	$purchase_report_details[] = 
	['Invoice Date'=>$purchase['Invoice_Date'],
	'Invoice No'=>$purchase['Invoice_No'],
	'Supplier Name'=>$purchase['Supplier_Name'],
	'Payment Mode'=>$purchase['paymentmode'],
	'Gross Amount'=>$crud->num_format($purchase['Total_Gross_Amount']),
	'Discount'=>$crud->num_format($purchase['Total_Discount_Amount']),
	'Net Amount'=>$crud->num_format($purchase['Total_Net_Amount']),
	'Tax Amount'=>$crud->num_format($purchase['Total_Tax_Amount']),
	'Total Amount'=>$crud->num_format($purchase['Total_Amount']),
	'Discount Amount'=>$crud->num_format($purchase['Discount_Amount']),
	'Grand Total'=>$crud->num_format($purchase['Grand_Total'])
	];
}
if(!empty($purchaselist_all)){
$purchase_report_details['sum_gross_amount'] = $crud->num_format($sum_gross_amount);
$purchase_report_details['sum_total_discount_amount'] = $crud->num_format($sum_total_discount_amount);
$purchase_report_details['sum_net_amount'] = $crud->num_format($sum_net_amount);
$purchase_report_details['sum_tax_amount'] = $crud->num_format($sum_tax_amount);
$purchase_report_details['sum_total_amount'] = $crud->num_format($sum_total_amount);
$purchase_report_details['sum_discount_amount'] = $crud->num_format($sum_discount_amount);
$purchase_report_details['sum_grand_total'] = $crud->num_format($sum_grand_total);
}

if(isset($_GET['export']) && $_GET['export'] == 1){
	 $report = new Report();	
	 $report_title = 'Purchase Report';
	 if(!empty($purchase_report_details)){
		$report->getReport($purchase_report_details,$report_title,$is_main=0);
	 }else{
		?> 
		<script>
			alert('No Record Found');
			window.location = window.location.href.split("?")[0];
		</script>
<?php	 }
	 
}
$bread_cums = ['Purchase'=>'purchase.php'];

include_once('menu.php');
?>
<div class="m-b-md">
	<h3 class="m-b-none">Purchase Report</h3>
</div>

<section class="panel panel-default">
	<header class="panel-heading">
	 Purchase List		
	</header>
	<div class="row wrapper">
	
	<form method="get">
			
	<div class="col-lg-2">
	<input name="inv_from_date" value="<?=isset($_GET['inv_from_date'])?$_GET['inv_from_date']:date('d-m-Y'); ?>" class="input-sm  datepicker-input form-control" size="16" type="text"  data-date-format="dd-mm-yyyy"  placeholder="From Date">
	</div>

	<div class="col-lg-2">
	<input name="inv_to_date" value="<?=isset($_GET['inv_to_date'])?$_GET['inv_to_date']:date('d-m-Y'); ?>" class="input-sm  datepicker-input form-control" size="16" type="text"  data-date-format="dd-mm-yyyy"  placeholder="To Date" >
	</div>

	<div class="col-lg-2 <?= $cnt == 1?'hidden':''?>">
		<?php /* 	<select name="company_id" class="input-sm form-control input-s-sm inline v-middle">
			<option value="0">All Company</option>
				<?php foreach ($company as $k => $com) {
					$select_pro='';
					if (isset($_GET['company_id'])) {
						$select_cat = (int) $_GET['company_id'] == $com['Company_Id']? 'selected="selected"':"" ;
					}						
					echo '<option '.$select_cat.' value="'.$com['Company_Id'].'">'.$com['Company_Name'].'</option>';
					}
				?>				
			</select> */ ?>
			<select name="branch_id" class="input-sm form-control input-s-sm inline v-middle">
			<option value="0">All Branch</option>
				<?php foreach ($branch as $k => $com) {
					$select_pro='';
					if (isset($_GET['branch_id'])) {
						$select_cat = (int) $_GET['branch_id'] == $com['Branch_Id']? 'selected="selected"':"" ;
					}						
					echo '<option '.$select_cat.' value="'.$com['Branch_Id'].'">'.$com['Branch_Name'].'</option>';
					}
				?>				
			</select>
	</div>
					<div class="col-lg-4">
						<div class="input-group">	
							<span class="input-group-btn">
								<button class="btn btn-sm btn-default" type="submit">Search</button>
								<button class="btn btn-sm btn-default" value="1" name="unset_filter">Clear</button>
							</span>
						</div>
					</div>					
					<div class="col-lg-2-3"></div>
					</form>	
					<div class="col-lg-1">
	<a class="btn btn-sm btn-info btn-export"  href="?export=1&<?=http_build_query($_GET, '', '&')?>">Export</a>
	</div>						
	</div>
	<div class="table-responsive">
		<table class="table b-t b-light">
			<thead>
				<tr class="tbl_th">
				    <th>S.NO</th>
					<th class="<?= $cnt == 1?'hidden':''?>">Branch</th>				
					<th>Invoice Date</th>
					<th>Gross Amount</th>
					<th>Discount</th>
					<th>Net Amount</th>
					<th>Tax Amount</th>
					<th>Total Amount</th>
					<th>Discount Amount</th>
					<th>Grand Total</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
							<?php						
							if ($purchase_list){
								foreach ($purchase_list as $k => $purchase) {
									$purchase['Total_Gross_Amount'] = $crud->num_format($purchase['Total_Gross_Amount']);
									$purchase['Total_Discount_Amount'] = $crud->num_format($purchase['Total_Discount_Amount']);
									$purchase['Total_Net_Amount'] = $crud->num_format($purchase['Total_Net_Amount']);
									$purchase['Total_Tax_Amount'] = $crud->num_format($purchase['Total_Tax_Amount']);
									$purchase['Total_Amount'] = $crud->num_format($purchase['Total_Amount']);
									$purchase['Discount_Amount'] = $crud->num_format($purchase['Discount_Amount']);
									$purchase['Grand_Total'] = $crud->num_format($purchase['Grand_Total']);
								    $cd = $counter++;
									echo '<tr data-purchase_id="'.$cd.'" class="bb" >
								<td>'.$counter++.'</td>
								<td class="'.($cnt == 1?'hidden':'').'">'.$purchase['Branch_Name'].'</td>
								<td>'.$purchase['invoice_date'].'</td>
								<td>'.$purchase['Total_Gross_Amount'].'</td>
								<td>'.$purchase['Total_Discount_Amount'].'</td>
								<td>'.$purchase['Total_Net_Amount'].'</td>
								<td>'.$purchase['Total_Tax_Amount'].'</td>
								<td>'.$purchase['Total_Amount'].'</td>
								<td>'.$purchase['Discount_Amount'].'</td>
								<td>'.$purchase['Grand_Total'].'</td>
								<td onclick="return click();"><a href="purchase_invoice.php?inv_date='.$crud->format_date($purchase['Invoice_Date']).'&cmp_id='.((isset($_GET['branch_id']) && !empty($_GET['branch_id']))?$_GET['branch_id']:0).'"><i class="fa fa-eye"></i></a></td>
									  </tr>';

									  
$sql_det="SELECT pm.Purchase_Master_Id,pm.Branch_Id,pm.Invoice_No,$invdate AS Invoice_Date,pm.Supplier_Name,pm.Is_Cash,CASE
WHEN pm.Is_Cash = 0 THEN 'Cash'
WHEN pm.Is_Cash = 1 THEN 'Card'
WHEN pm.Is_Cash = 2 THEN 'Credit'
WHEN pm.Is_Cash = 3 THEN 'Multi'
ELSE 'Cash'
END as paymentmode,pm.Total_Gross_Amount,pm.Total_Discount_Amount,pm.Total_Net_Amount,pm.Total_Tax_Amount,pm.Total_Amount,pm.Discount_Amount,pm.Grand_Total 
FROM purchase_master pm where pm.Invoice_Date = '".$purchase['Invoice_Date']."' and is_saved='true' and pm.Branch_Id = '".$purchase['Branch_Id']."'
$where order by pm.Purchase_Master_Id desc ";

$purchase_det_list = $crud->getData($sql_det);


if ($purchase_det_list){
	echo '<tr class="stbl fd_'.$cd.'">
	<td colspan="10" class="sub_row_td">
	<table class="table b-t b-light tbl_mr">
	<thead>
	<tr class="stbl_th">
	<th>Invoice Date</th>
    <th>Invoice No</th>
	<th>Supplier Name</th>
	<th>Payment Mode</th>
	<th>Gross Amount</th>
	<th>Discount</th>
	<th>Net Amount</th>
	<th>Tax Amount</th>
	<th>Total Amount</th></tr></thead>
	<tbody>';
	
	foreach ($purchase_det_list as $k => $purchase_detl) {
	  $purchase_detl['Total_Gross_Amount'] = $crud->num_format($purchase_detl['Total_Gross_Amount']);
	  $purchase_detl['Total_Discount_Amount'] = $crud->num_format($purchase_detl['Total_Discount_Amount']);
	  $purchase_detl['Total_Net_Amount'] = $crud->num_format($purchase_detl['Total_Net_Amount']);
	  $purchase_detl['Total_Tax_Amount'] = $crud->num_format($purchase_detl['Total_Tax_Amount']);
	  $purchase_detl['Total_Amount'] = $crud->num_format($purchase_detl['Total_Amount']);

   echo  '<tr data-purchase_id="'.$cd.'" class="stbl ff fd_'.$cd.'">
   <td>'.$crud->disp_date($purchase_detl['Invoice_Date']).'</td>
   <td>'.$purchase_detl['Invoice_No'].'</td>
   <td>'.$purchase_detl['Supplier_Name'].'</td>
   <td>'.$purchase_detl['paymentmode'].'</td>
   <td>'.$purchase_detl['Total_Gross_Amount'].'</td>
   <td>'.$purchase_detl['Total_Discount_Amount'].'</td>
   <td>'.$purchase_detl['Total_Net_Amount'].'</td>
   <td>'.$purchase_detl['Total_Tax_Amount'].'</td>
   <td>'.$purchase_detl['Total_Amount'].'</td></tr>';

$sql="SELECT pd.Purchase_Detail_Id,pd.Purchase_Master_Id,pd.Product_Id,pm.Product_name,pm.Product_Code,pd.Quantity,pd.Purchase_Rate,pd.Gross_Amount,pd.Discount,pd.Net_Amount,pd.Tax_Amount,pd.Amount FROM purchase_detail pd 
left join Product_Master pm on pd.Product_Id=pm.Product_Id
where pd.Purchase_Master_Id = ".$purchase['Purchase_Master_Id']."  order by pd.Purchase_Detail_Id desc ";


	
	$purchase_detail_list = $crud->getData($sql);
										if ($purchase_detail_list){
										  echo '<tr class="stbl sd_'.$purchase['Purchase_Master_Id'].'">
										  <td colspan="10" class="sub_row_td">
	                                      <table class="table b-t b-light tbl_mr">
										  <thead>
										  <tr class="stbl2_th">
										  <th>Product Code</th>
										  <th>Product Name</th>
										  <th>Quantity</th>
										  <th>Rate</th>
										  <th>Gross Amount</th>
										  <th>Discount Amount</th>
										  <th>Net Amount</th>
										  <th>Tax Amount</th>
										  <th>Total Amount</th></tr></thead>
										  <tbody>';
										  foreach ($purchase_detail_list as $k => $purchase_detail) {
	 										$purchase_detail['Purchase_Rate'] = $crud->num_format($purchase_detail['Purchase_Rate']);
	 										$purchase_detail['Gross_Amount'] = $crud->num_format($purchase_detail['Gross_Amount']);
											$purchase_detail['Discount'] = $crud->num_format($purchase_detail['Discount']);
	 										$purchase_detail['Net_Amount'] = $crud->num_format($purchase_detail['Net_Amount']);
	 										$purchase_detail['Tax_Amount'] = $crud->num_format($purchase_detail['Tax_Amount']);
	 										$purchase_detail['Amount'] = $crud->num_format($purchase_detail['Amount']);
	
	 									 echo  '<tr class="stbl2 sd_'.$purchase['Purchase_Master_Id'].'">
	 									  <td>'.$purchase_detail['Product_Code'].'</td>
	 									  <td>'.$purchase_detail['Product_name'].'</td>
	 									  <td>'.$purchase_detail['Quantity'].'</td>
	 									  <td>'.$purchase_detail['Purchase_Rate'].'</td>
	 									  <td>'.$purchase_detail['Gross_Amount'].'</td>
	 									  <td>'.$purchase_detail['Discount'].'</td>
	 									  <td>'.$purchase_detail['Net_Amount'].'</td>
	 									  <td>'.$purchase_detail['Tax_Amount'].'</td>
	 									  <td>'.$purchase_detail['Amount'].'</td></tr>';
	 									  }
										  
	                                    echo '</tbody></table></td></tr/>';
										 
	 									} else{
	
	 										echo  '<tr class="td_error sub_row hidden aa_'.$purchase['Purchase_Master_Id'].'">
											<td colspan="'.($cnt == 1?'10':'11').'">No Record Found</td></tr>';
	
	 									}


									}

									echo '</tbody></table></td></tr/>';

   
  } else{

	  echo  '<tr class="td_error sub_row hidden aa_'.$purchase['Purchase_Master_Id'].'">
	  <td colspan="10">No Record Found</td></tr>';

  } 

									  
									  
								}
								echo  '<tr>
									  <td colspan="'.($cnt == 1?'2':'3').'"></td>
									  <td><b>'.$purchase_report_details['sum_gross_amount'].'</b></td>
									  <td><b>'.$purchase_report_details['sum_total_discount_amount'].'</b></td>
									  <td><b>'.$purchase_report_details['sum_net_amount'].'</b></td>
									  <td><b>'.$purchase_report_details['sum_tax_amount'].'</b></td>
									  <td><b>'.$purchase_report_details['sum_total_amount'].'</b></td>
									  <td><b>'.$purchase_report_details['sum_discount_amount'].'</b></td>
									  <td><b>'.$purchase_report_details['sum_grand_total'].'</b></td></tr>';
							} else {

								echo  '<tr class="td_error ">
								       <td colspan="'.($cnt == 1?'10':'11').'">No Record Found</td></tr>';

							}
						
			?>			
			</tbody>
		</table>
	</div>
	<footer class="panel-footer">
		<div class="row">	
		<?php
		if($total_rows != 0)
			{
				$_from = $offset+1;
				$_to = ($offset+$no_of_records_per_page) > $total_rows ? $total_rows: $offset+$no_of_records_per_page;
		?>
		
				<div class="col-sm-8 text-center">							
							<small class="text-muted inline m-t-sm m-b-sm  txt_page_cnt">Total Purchase : <?=$total_rows?><br>Showing From :<?=$_from?> To <?=$_to?></small>
							
													
			</div>
			<?php }	?>
			<?php
		if($total_rows != 0)
			{
		?>
			<div class="col-sm-4 text-right text-center-xs">
			<?php	
							$params=[];
							
							if (isset($_GET['inv_from_date'])) {
								$params['inv_from_date'] = $_GET['inv_from_date'];
							}
							
							if (isset($_GET['inv_to_date'])) {
								$params['inv_to_date'] = $_GET['inv_to_date'];
							}	

							if(isset($_GET['branch_id'])) {
								$params['branch_id'] = (int) $_GET['branch_id'];
							}

							$url_params = sizeof($params)>0?'&'.http_build_query($params):'';			
						?>
							<ul class="pagination">
								<li>
									<a href="?pageno=1<?=$url_params?>">First</a></li>
								<li class="<?php
							if ($pageno <= 1) {
								echo 'disabled'; } ?>">
									<a href="<?php
							if ($pageno <= 1) {
								echo '#'; } else {
										echo "?pageno=".($pageno - 1)."".$url_params; } ?>"<?=$url_params ?>>Prev</a>
								</li>
								<li class="<?php
							if ($pageno >= $total_pages) {
								echo 'disabled'; } ?>">
									<a href="<?php
							if ($pageno >= $total_pages) {
								echo '#'; } else {
										echo "?pageno=".($pageno + 1)."".$url_params; } ?>">Next</a>
								</li>
								<li>
									<a href="?pageno=<?php echo $total_pages."".$url_params; ?>">Last</a></li>
							</ul>
			</div>
			<?php }	?>
		</div>
	</footer>
</section>
<?php  include_once('footer.php'); ?>
 <script>
$( document ).ready(function() {
 $(".bb").on('click',function(){	
	 var s_id = $(this).data('purchase_id');
	 $(this).parent().find(".fd_"+s_id).toggleClass("active");
 });
 $(".ff").on('click',function(){
	$(this).closest('tr').next('tr').toggleClass("active");
 });
});
</script>