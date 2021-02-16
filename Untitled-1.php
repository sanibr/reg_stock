<?php
include_once("check_login.php");
include_once('class/common_settings.php');
include_once("class/Crud.php");
include_once("class/Report.php");

$crud = new Crud();

include_once('menu.php');
?>
<div class="m-b-md">
	<h3 class="m-b-none">Sales Report</h3>
</div>

<section class="panel panel-default">
                <header class="panel-heading">
                  DataTables 
                  <i class="fa fa-info-sign text-muted" data-toggle="tooltip" data-placement="bottom" data-title="ajax to load the data."></i> 
                </header>
                <div class="row wrapper">
	
	<form method="get">
			
	<div class="col-lg-2">
	<input type="text" name="filter_voucher_no" id="filter_voucher_no"  class="input-sm form-control" placeholder="Voucher No">
	</div>

  <div class="col-lg-2">
	<input name="filter_transaction_date" id="filter_transaction_date"  class="input-sm  datepicker-input form-control" size="16" type="text"  data-date-format="dd-mm-yyyy"  placeholder="Transaction Date">
	</div>


					<div class="col-lg-4">
						<div class="input-group">	
							<span class="input-group-btn">
								<button class="btn btn-sm btn-default" type="submit" id="filter">Search</button>
								<button class="btn btn-sm btn-default" value="1" name="unset_filter">Clear</button>
							</span>
						</div>
					</div>					
					<div class="col-lg-2-4"></div>
					</form>	
                    <div class="col-lg-1">
	<a class="btn btn-sm btn-info btn-export"  href="?export=1&<?=http_build_query($_GET, '', '&')?>">Export</a>
	</div>							
	</div>
                <div class="table-responsive">
                  <table id="stock_data" class="table table-striped m-b-none" >
                    <thead>
                      <tr>
                        <th width="20%">Invoice Date</th>
                        <th width="25%">Invoice No</th>
                        <th width="25%">Gross Amount</th>
                        <th width="15%">Discount</th>
                        <th width="15%">Net Amount</th>
                      </tr>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
                </div>
              </section>
<?php  include_once('footer.php'); ?>
<script>
 $(document).ready(function(){

  fill_datatable();
  
  function fill_datatable(filter_voucher_no = '', filter_transaction_date = '')
  {
   var dataTable = $('#stock_data').DataTable({
    "processing" : true,
    "serverSide" : true,
    "order" : [],
    "searching" : false,
    "ajax" : {
     url:"Crud.php?method=stock_data()",
     type:"POST",
     data:{
      filter_voucher_no:filter_voucher_no, filter_transaction_date:filter_transaction_date
     }
    }
   });
  }
  
  $('#filter').click(function(){
   var filter_voucher_no = $('#filter_voucher_no').val();
   var filter_transaction_date = $('#filter_transaction_date').val();
   if(filter_voucher_no != '' && filter_transaction_date != '')
   {
    $('#stock_data').DataTable().destroy();
    fill_datatable(filter_voucher_no, filter_transaction_date);
   }
   else
   {
    alert('Select Both filter option');
    $('#stock_data').DataTable().destroy();
    fill_datatable();
   }
  });
  
  
 });
 
</script>
