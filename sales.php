<?php
include_once("check_login.php");
include_once('class/common_settings.php');
include_once("class/Crud.php");
include_once("class/Report.php");

$crud = new Crud();

$where = "";


if ((isset($_GET['unset_filter']) && $_GET['unset_filter']==1 )) {
	$_GET['inv_from_date'] = date('d-m-Y');
	$_GET['inv_to_date'] = date('d-m-Y');
	$_GET['branch_id'] = "";
}
if(empty($_GET['inv_from_date']) && empty($_GET['inv_to_date'])){
	$_GET['inv_from_date'] = date('d-m-Y');
	$_GET['inv_to_date'] = date('d-m-Y');
}
if (isset($_GET['inv_from_date']) && !empty($_GET['inv_from_date']) || isset($_GET['inv_to_date']) && !empty($_GET['inv_to_date']) ) {
    if($_SESSION['db_type'] == "mysql" || !isset($_SESSION['db_type'])){
		$where.=" and date(sm.Invoice_Date)  >='".$crud->format_date($_GET['inv_from_date'])."' and date(sm.Invoice_Date)  <='".$crud->format_date($_GET['inv_to_date'])."'";
    } else if ($_SESSION['db_type'] == "mssql"){
        $where.=" and sm.Invoice_Date  >='".$crud->format_date($_GET['inv_from_date'])."' and sm.Invoice_Date  <='".$crud->format_date($_GET['inv_to_date'])."'";
    }
}

// if (isset($_GET['company_id']) && !empty($_GET['company_id'])) {
// 	$where.=" and sm.Company_Id = ".(int) $_GET['company_id'];
// }

// $company = $crud->getData("select Company_Id,Company_Name from company order by Company_Name");
// $company_count = $crud->getData("SELECT COUNT(DISTINCT company_id) AS com_count FROM sales_master");
// $cnt = $company_count[0]['com_count'];

if (isset($_GET['branch_id']) && !empty($_GET['branch_id'])) {
	$where.=" and sm.Branch_Id = ".(int) $_GET['branch_id'];
}

$branch = $crud->getData("select Branch_Id,Branch_Name from branch order by Branch_Name");
$branch_count = $crud->getData("SELECT COUNT(DISTINCT branch_id) AS brn_count FROM sales_master");
$cnt = $branch_count[0]['brn_count'];

$no_of_records_per_page = 10;

if (isset($_GET['pageno'])) {
	$pageno = $_GET['pageno'];
    $counter = ($pageno-1) * $no_of_records_per_page+1;
    $sal_no = ($pageno-1) * $no_of_records_per_page+1;
} else {
    $counter = 1;
    $sal_no = 1;
	$pageno = 1;
}

$no_of_records_per_page = 10;
$offset = ($pageno-1) * $no_of_records_per_page; 

   if($_SESSION['db_type'] == "mysql" || !isset($_SESSION['db_type'])){
		
		$invdate = "DATE_FORMAT(sm.Invoice_Date,'%d/%m/%Y')";
		
    } else if ($_SESSION['db_type'] == "mssql"){
    
        $invdate = "FORMAT(sm.Invoice_Date,'dd/MM/yyyy')";
      
    }

$sql_rep="SELECT sm.Sales_Master_Id,sm.Serial_No,$invdate AS Invoice_Date,sm.Invoice_No,sm.Customer_Name,sm.IsCash,CASE
WHEN sm.IsCash = 0 THEN 'Cash'
WHEN sm.IsCash = 1 THEN 'Card'
WHEN sm.IsCash = 2 THEN 'Credit'
WHEN sm.IsCash = 3 THEN 'Multi'
ELSE 'Cash'
END as paymentmode,sm.Total_Gross_Amount,sm.Total_Discount_Amount,sm.Total_Net_Amount,sm.Total_Tax_Amount,sm.Total_Amount,sm.Discount_Amount,sm.Grand_Total 
FROM sales_master sm 
left join customer sc on sm.Customer_Id=sc.Customer_Id where 1=1 and is_saved='true'
$where order by sm.Sales_Master_Id desc ";


$sql="SELECT sm.Company_Id,c.Company_Name,sm.Branch_Id,b.Branch_Name,sm.Invoice_Date,sum(sm.Total_Gross_Amount) as Total_Gross_Amount,sum(sm.Total_Discount_Amount) as Total_Discount_Amount,sum(sm.Total_Net_Amount) as Total_Net_Amount,sum(sm.Total_Tax_Amount) as Total_Tax_Amount,sum(sm.Total_Amount) as Total_Amount,sum(sm.Discount_Amount) as Discount_Amount,sum(sm.Grand_Total) as Grand_Total 
FROM sales_master sm 
left join customer sc on sm.Customer_Id=sc.Customer_Id 
left join company c on sm.Company_Id=c.Company_Id 
left join branch b on sm.Branch_Id=b.Branch_Id
where 1=1 and is_saved='true'
$where group by sm.Company_Id,c.Company_Name,sm.Branch_Id,b.Branch_Name,sm.Invoice_Date order by sm.Invoice_Date desc";



$total_rows = $crud->number_of_records($sql);
$total_pages = ceil($total_rows / $no_of_records_per_page);

   if($_SESSION['db_type'] == "mysql" || !isset($_SESSION['db_type'])){
       
	    $sql_data = "$sql LIMIT $offset, $no_of_records_per_page";
	    
    } else if ($_SESSION['db_type'] == "mssql"){
        
        
        $sql_data = "$sql OFFSET $offset ROWS FETCH NEXT $no_of_records_per_page ROWS ONLY ";
    }
    
$sales_list = $crud->getData($sql_data);

$saleslist_all = $crud->getData($sql_rep);
$sum_gross_amount = 0;

foreach ($saleslist_all as $k => $sales) {
	$sum_gross_amount += $sales['Total_Gross_Amount'];
	$sum_total_discount_amount += $sales['Total_Discount_Amount'];
	$sum_net_amount += $sales['Total_Net_Amount'];
	$sum_tax_amount += $sales['Total_Tax_Amount'];
	$sum_total_amount += $sales['Total_Amount'];
	$sum_discount_amount += $sales['Discount_Amount'];
	$sum_grand_total += $sales['Grand_Total'];
	$sales_report_details[] = 
	['Invoice Date'=>$sales['Invoice_Date'],
	'Invoice No'=>$sales['Invoice_No'],
	'Customer Name'=>$sales['Customer_Name'],
	'Payment Mode'=>$sales['paymentmode'],
	'Gross Amount'=>$crud->num_format($sales['Total_Gross_Amount']),
	'Discount'=>$crud->num_format($sales['Total_Discount_Amount']),
	'Net Amount'=>$crud->num_format($sales['Total_Net_Amount']),
	'Tax Amount'=>$crud->num_format($sales['Total_Tax_Amount']),
	'Total Amount'=>$crud->num_format($sales['Total_Amount']),
	'Discount Amount'=>$crud->num_format($sales['Discount_Amount']),
	'Grand Total'=>$crud->num_format($sales['Grand_Total'])
	];
}
if(!empty($saleslist_all)){
	$sales_report_details['sum_gross_amount'] = $crud->num_format($sum_gross_amount);
	$sales_report_details['sum_total_discount_amount'] = $crud->num_format($sum_total_discount_amount);
	$sales_report_details['sum_net_amount'] = $crud->num_format($sum_net_amount);
	$sales_report_details['sum_tax_amount'] = $crud->num_format($sum_tax_amount);
	$sales_report_details['sum_total_amount'] = $crud->num_format($sum_total_amount);
	$sales_report_details['sum_discount_amount'] = $crud->num_format($sum_discount_amount);
	$sales_report_details['sum_grand_total'] = $crud->num_format($sum_grand_total);
}


if(isset($_GET['export']) && $_GET['export'] == 1){
	 $report = new Report();	
	 $report_title = 'Sales Report';
	
	 if(!empty($sales_report_details)){
		$report->getReport($sales_report_details,$report_title);
	 }else{
		?> 
		<script>
			alert('No Record Found');
			window.location = window.location.href.split("?")[0];
		</script>
<?php	 }
}
$bread_cums = ['Sales'=>'sales.php'];

include_once('menu.php');
?>
<div class="m-b-md">
	<h3 class="m-b-none">Sales Report</h3>
</div>

<section class="panel panel-default">
	<header class="panel-heading">
		Sales List		
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
		<?php /*	<select name="company_id" class="input-sm form-control input-s-sm inline v-middle">
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
							if ($sales_list){
                                echo '<div class="loader" style="display:none"></div> ';
								foreach ($sales_list as $k => $sales) {
									$sales['Total_Gross_Amount'] = $crud->num_format($sales['Total_Gross_Amount']);
									$sales['Total_Discount_Amount'] = $crud->num_format($sales['Total_Discount_Amount']);
									$sales['Total_Net_Amount'] = $crud->num_format($sales['Total_Net_Amount']);
									$sales['Total_Tax_Amount'] = $crud->num_format($sales['Total_Tax_Amount']);
									$sales['Total_Amount'] = $crud->num_format($sales['Total_Amount']);
									$sales['Discount_Amount'] = $crud->num_format($sales['Discount_Amount']);
									$sales['Grand_Total'] = $crud->num_format($sales['Grand_Total']);
								    $inv_date = $sales['Invoice_Date'];
									$salmas_id = $sal_no++;
									echo '<tr data-sales_id="'.$salmas_id.'" data-sales_inv="'.$sales['Invoice_Date'].'" data-sales_where="'.$where.'" class="bb"  >
								<td>'.$counter++.'</td>
								<td class="'.($cnt == 1?'hidden':'').'">'.$sales['Branch_Name'].'</td>
								<td>'.$crud->disp_date($sales['Invoice_Date']).'</td>
								<td>'.$sales['Total_Gross_Amount'].'</td>
								<td>'.$sales['Total_Discount_Amount'].'</td>
								<td>'.$sales['Total_Net_Amount'].'</td>
								<td>'.$sales['Total_Tax_Amount'].'</td>
								<td>'.$sales['Total_Amount'].'</td>
								<td>'.$sales['Discount_Amount'].'</td>
								<td>'.$sales['Grand_Total'].'</td>
								<td onclick="return click();"><a href="sales_invoice.php?inv_date='.$crud->format_date($sales['Invoice_Date']).'&cmp_id='.((isset($_GET['branch_id']) && !empty($_GET['branch_id']))?$_GET['branch_id']:0).'"><i class="fa fa-eye"></i></a></td>
								</tr>';
									  

									  $sql_det="SELECT sm.Sales_Master_Id,sm.Serial_No,sm.Invoice_Date,sm.Invoice_No,sm.Customer_Name,sm.IsCash,CASE
                                        WHEN sm.IsCash = 0 THEN 'Cash'
                                        WHEN sm.IsCash = 1 THEN 'Card'
                                        WHEN sm.IsCash = 2 THEN 'Credit'
                                        WHEN sm.IsCash = 3 THEN 'Multi'
                                        ELSE 'Cash'
                                        END as paymentmode,sm.Total_Gross_Amount,sm.Total_Discount_Amount,sm.Total_Net_Amount,sm.Total_Tax_Amount,sm.Total_Amount 
                                        FROM sales_master sm 
                                        left join customer sc on sm.Customer_Id=sc.Customer_Id where sm.Invoice_Date = '".$sales['Invoice_Date']."' and is_saved='true'
                                        $where order by sm.Sales_Master_Id desc ";

                                        $sales_det_list = $crud->getData($sql_det);

                                    if ($sales_det_list){
                                        echo '<tr class="stbl fd_'.$salmas_id.'">
                                            <td colspan="11" class="sub_row_td">
                                            <table class="table b-t b-light tbl_mr" id="saldetail'.$salmas_id.'">'; 
                                        echo '</table></td></tr/>';

                                    } else{

                                        echo  '<tr class="td_error sub_row hidden aa_'.$salmas_id.'">
                                        <td colspan="10">No Record Found</td></tr>';

                                    } 	  
									  
								}
								echo  '<tr>
			                          <td colspan="'.($cnt == 1?'2':'3').'"></td>
									  <td><b>'.$sales_report_details['sum_gross_amount'].'</b></td>
									  <td><b>'.$sales_report_details['sum_total_discount_amount'].'</b></td>
									  <td><b>'.$sales_report_details['sum_net_amount'].'</b></td>
									  <td><b>'.$sales_report_details['sum_tax_amount'].'</b></td>
									  <td><b>'.$sales_report_details['sum_total_amount'].'</b></td>
									  <td><b>'.$sales_report_details['sum_discount_amount'].'</b></td>
									  <td><b>'.$sales_report_details['sum_grand_total'].'</b></td></tr>';
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
							<small class="text-muted inline m-t-sm m-b-sm  txt_page_cnt">Total Sales : <?=$total_rows?><br>Showing From :<?=$_from?> To <?=$_to?></small>
							
													
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
<style>
.loader {
  border: 16px solid #f3f3f3;
  border-radius: 50%;
  border-top: 8px solid #717171;
  border-bottom: 8px solid #717171;
  width: 25px;
  height: 25px;
  -webkit-animation: spin 2s linear infinite;
  animation: spin 2s linear infinite;
  margin: auto;
}

@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>
 <script>
$( document ).ready(function() {
    $(".bb").on('click',function(){	
	 var s_id = $(this).data('sales_id');
	 var s_inv = $(this).data('sales_inv');
	 var s_where = $(this).data('sales_where');
     $('.loader').show(); 
	 $(this).parent().find(".fd_"+s_id).toggleClass("active");
	 $.ajax({    //create an ajax request to display.php
		type: "POST",
		url: "sales_detail_view.php",             
		dataType: "html",   //expect html to be returned 
		data: {s_id:s_id,s_inv:s_inv,where:s_where},               
		success: function(response){                    
			$("#saldetail"+s_id).html(response);
            $('.loader').hide(); 
			//alert(response);
		}
     });
 });

});
</script>