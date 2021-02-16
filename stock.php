<?php
include_once("check_login.php");
include_once('class/common_settings.php');
include_once("class/Crud.php");
include_once("class/Report.php");

$crud = new Crud();

$where = "";

if (isset($_GET['unset_filter']) && $_GET['unset_filter']==1) {
	$_GET['product_id'] = "";
	$_GET['category_id'] = "";
	$_GET['brand_id'] = "";
}else{
	if (isset($_GET['product_id']) && !empty($_GET['product_id'])) {
		$where.=" and pd.Product_Detail_Id = ".(int) $_GET['product_id'];
	}
	if (isset($_GET['category_id']) && !empty($_GET['category_id'])) {
		list($category_id,$company_id) = explode("_",$_GET['category_id']);
		$where.=" and pm.Category_ID = ".(int) $category_id." and tc.Company_Id = ".$company_id;
	}
	if (isset($_GET['brand_id']) && !empty($_GET['brand_id'])) {
	    list($band_id,$company_id) = explode("_",$_GET['brand_id']);
		$where.=" and pm.Brand_ID = ".(int) $band_id." and tb.Company_Id = ".$company_id;
	}
}

$product = $crud->getData("select Product_Detail_Id,Product_Detail_Name from product_detail order by Product_Detail_Name");

$item_category = $crud->getData("select Category_ID,Category_Name,Company_Id from item_category order by Category_Name");

$item_brand = $crud->getData("select Brand_ID,Brand_Name,Company_Id from item_brand order by Brand_Name");


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

$sql="SELECT pd.Product_Detail_Id,pd.Product_Id,pd.Product_Code,pd.Product_Detail_Name,tb.Brand_Name,tc.Category_Name,pm.Opening_Stock,pd.Branch_Id,b.Branch_Name,sd.sales_quantity,srd.sales_return_quantity,pud.purchase_quantity,purd.purchase_return_quantity,st.Adjust_Add,st.Adjust_Minus,pd.Stock, pd.Purchase_Cost,(pd.Stock*pd.Purchase_Cost) AS stock_value 
FROM product_detail pd
    LEFT JOIN product_master pm ON pd.Product_Id=pm.Product_Id
    LEFT JOIN item_brand tb ON pm.Brand_ID=tb.Brand_ID
    LEFT JOIN item_category tc ON pm.Category_ID=tc.Category_ID
    LEFT JOIN branch b on pd.Branch_Id=b.Branch_Id
    LEFT JOIN (SELECT Product_Detail_Id,SUM(Quantity) AS sales_quantity FROM sales_detail GROUP BY Product_Detail_Id ) sd 
    ON pd.Product_Detail_Id=sd.Product_Detail_Id
    LEFT JOIN (SELECT Product_Detail_Id,SUM(Quantity) AS sales_return_quantity FROM salesreturn_detail GROUP BY Product_Detail_Id ) srd 
    ON pd.Product_Detail_Id=srd.Product_Detail_Id
    LEFT JOIN (SELECT Product_Detail_Id,SUM(Quantity) AS purchase_quantity FROM purchase_detail GROUP BY Product_Detail_Id ) pud 
    ON pd.Product_Detail_Id=pud.Product_Detail_Id
    LEFT JOIN (SELECT Product_Detail_Id,SUM(Quantity) AS purchase_return_quantity FROM purchasereturn_detail GROUP BY Product_Detail_Id ) purd 
    ON pd.Product_Detail_Id=purd.Product_Detail_Id  
    LEFT JOIN (SELECT Product_Detail_Id,SUM(Adjust_Quantity_Add) AS Adjust_Add,SUM(Adjust_Quantity_Minus) AS Adjust_Minus FROM stock_adj_detail GROUP BY Product_Detail_Id ) st 
    ON pd.Product_Detail_Id=st.Product_Detail_Id where 1=1
	$where order by pd.Product_Detail_Id desc ";

	
$total_rows = $crud->number_of_records($sql);
$total_pages = ceil($total_rows / $no_of_records_per_page);
if(isset($_GET['export']) && $_GET['export'] == 1){
    $stock_list = $crud->getData("$sql");
} else{
        if($_SESSION['db_type'] == "mysql" || !isset($_SESSION['db_type'])){
       
	        $sql_data = "$sql LIMIT $offset, $no_of_records_per_page";
	    
        } else if ($_SESSION['db_type'] == "mssql"){
            
            $sql_data = "$sql OFFSET $offset ROWS FETCH NEXT $no_of_records_per_page ROWS ONLY";
        }
    
    $stock_list = $crud->getData($sql_data);
	
}

$stocklist_all = $crud->getData($sql);
$sum_gross_amount = 0;
foreach ($stocklist_all as $k => $stock) {
	$sum_stock_value += $stock['stock_value'];
	$sum_purchase_cost += $stock['Purchase_Cost'];
}

if(!isset($_GET['export'])){
$bread_cums = ['Stock'=>'stock.php'];

include_once('menu.php');
?>
<div class="m-b-md">
	<h3 class="m-b-none">Stock Report</h3>
</div>

<section class="panel panel-default">
	<header class="panel-heading">
	 Stock List		
	</header>
	<div class="row wrapper">
	
	<form method="get">
			
    <div class="col-lg-2">
			<select name="product_id" class="input-sm form-control input-s-sm inline v-middle">
			<option value="">Product Name</option>
				<?php foreach ($product as $k => $pro) {
					$select_pro='';
					if (isset($_GET['product_id'])) {
						$select_pro = (int) $_GET['product_id'] == $pro['Product_Detail_Id']? 'selected="selected"':"" ;
					}						
					echo '<option '.$select_pro.' value="'.$pro['Product_Detail_Id'].'">'.$pro['Product_Detail_Name'].'</option>';
					}
				?>				
			</select>
	</div>

    <div class="col-lg-2">
			<select name="category_id" class="input-sm form-control input-s-sm inline v-middle">
			<option value="">Category</option>
				<?php foreach ($item_category as $k => $category) {
					$select_cat='';
					if (isset($_GET['category_id'])) {
						$category_id = $category['Category_ID'].'_'.$category['Company_Id'];
						$select_cat = $_GET['category_id'] == $category_id? 'selected="selected"':"" ;
					}						
					echo '<option '.$select_cat.' value="'.$category['Category_ID'].'_'.$category['Company_Id'].'">'.$category['Category_Name'].'</option>';
					}
				?>				
			</select>
	</div>


    <div class="col-lg-2">
			<select name="brand_id" class="input-sm form-control input-s-sm inline v-middle">
			<option value="">Brand</option>
				<?php foreach ($item_brand as $k => $brand) {
					$select_bra='';
					if (isset($_GET['brand_id'])) {
						$brand_id = $brand['Brand_ID'].'_'.$brand['Company_Id'];
						$select_bra = $_GET['brand_id'] == $brand_id? 'selected="selected"':"" ;
					}						
					echo '<option '.$select_bra.' value="'.$brand_id.'">'.$brand['Brand_Name'].'</option>';
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
                <? } $html= ''; 
				$pdf_tbl =  isset($_GET['export'])?'cellpadding="0" cellspacing="0" border="1" style="border:solid 1px; border-color:black;font-size: 18px;"':'';
				$pdf_td =  isset($_GET['export'])?'style="height:25px; padding:5px 5px;border:solid 1px; border-color:black;"':'';
				$cat_head  =  isset($_GET['export'])?'':'<th>Category</th>';
				$brand_head  =  isset($_GET['export'])?'':'<th>Brand</th>';
		$html .='<table '.$pdf_tbl.'  class="table b-t b-light table-striped">
			<thead>
				<tr class="">
				    <th>S.NO</th>				
					<th>PRD Code</th>
					<th>PRD Name</th>
					'.$cat_head.'
			        <th>Branch</th>
					<th>OP Stock</th>
					<th>S QTY</th>
					<th>SR QTY</th>
					<th>PUR QTY</th>
                    <th>PR.QTY</th>
                    <th>ADJ.Add</th>
                    <th>ADJ.Minus</th>
                    <th>CUR.STOCK</th>
                    <th>PU Cost</th>
                    <th>Stock Value</th>
				</tr>
			</thead>
			<tbody>';
                            
							if ($stock_list){
								foreach ($stock_list as $k => $stock) {
								$stock['stock_value'] = $crud->num_format($stock['stock_value']);
								$cat_field  =  isset($_GET['export'])?'':'<td '.$pdf_td .'>'.$stock['Category_Name'].'</td>';
								$brand_field  =  isset($_GET['export'])?'':'<td '.$pdf_td .'>'.$stock['Brand_Name'].'</td>';
								$html .='<tr data-stock_id="'.$stock['Product_Detail_Id'].'" class="bb" >
								<td '.$pdf_td.'>'.$counter++.'</td>
								<td '.$pdf_td.'>'.$stock['Product_Code'].'</td>
								<td '.$pdf_td.'>'.$stock['Product_Detail_Name'].'</td>
								'.$cat_field.'
								<td '.$pdf_td.'>'.$stock['Branch_Name'].'</td>
								<td '.$pdf_td.'>'.$crud->num_format($stock['Opening_Stock']).'</td>
								<td '.$pdf_td.'>'.$crud->num_format($stock['sales_quantity']).'</td>
								<td '.$pdf_td.'>'.$crud->num_format($stock['sales_return_quantity']).'</td>
                                <td '.$pdf_td.'>'.$crud->num_format($stock['purchase_quantity']).'</td>
                                <td '.$pdf_td.'>'.$crud->num_format($stock['purchase_return_quantity']).'</td>
                                <td '.$pdf_td.'>'.$crud->num_format($stock['Adjust_Add']).'</td>
                                <td '.$pdf_td.'>'.$crud->num_format($stock['Adjust_Minus']).'</td>
                                <td '.$pdf_td.'>'.$crud->num_format($stock['Stock']).'</td>
                                <td '.$pdf_td.'>'.$crud->num_format($stock['Purchase_Cost']).'</td>
                                <td '.$pdf_td.'>'.$crud->num_format($stock['stock_value']).'</td>
                                      </tr>';
                                        
								}
								$html .= '<tr>
			                          <td colspan="13"></td>
                                      <td '.$pdf_td.'><b>'.$crud->num_format($sum_purchase_cost).'</b></td>
                                      <td '.$pdf_td.'><b>'.$crud->num_format($sum_stock_value).'</b></td></tr>';
							} else {

								$html .=  '<tr class="td_error ">
										<td '.$pdf_td.' colspan="15">No Record Found</td></tr>';

							}
						
				
                            $html .= '</tbody></table>';
           
        
         if(isset($_GET['export']) && $_GET['export'] == 1){
            $report = new Report();	
			$report_title = 'Stock Report';
            if(!empty($html)){
               $report->getReport($html ,$report_title, 1);
            }else{
               ?> 
               <script>
                   alert('No Record Found');
                   window.location = window.location.href.split("?")[0];
               </script>
       <?php }            
       }else{
        echo $html;
       }
        ?>
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
							<small class="text-muted inline m-t-sm m-b-sm  txt_page_cnt">Total Stock : <?=$total_rows?><br>Showing From :<?=$_from?> To <?=$_to?></small>
							
													
			</div>
			<?php }	?>
			<?php
		if($total_rows != 0)
			{
		?>
			<div class="col-sm-4 text-right text-center-xs">
			<?php	
							$params=[];
							
							if(isset($_GET['product_id'])) {
								$params['product_id'] = (int) $_GET['product_id'];
							}

							if(isset($_GET['category_id'])) {
								$params['category_id'] = (int) $_GET['category_id'];
							}

							if(isset($_GET['brand_id'])) {
								$params['brand_id'] = (int) $_GET['brand_id'];
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
