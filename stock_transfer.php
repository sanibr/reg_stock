<?php
include_once("check_login.php");
include_once('class/common_settings.php');
include_once("class/Crud.php");
include_once("class/Report.php");

$crud = new Crud();

include_once('menu.php');

$branch = $crud->getData("select Branch_Id,Branch_Name from branch order by Branch_Name");
?>
<style>
  .auto_list{
  background-color:#eee;  
  cursor:pointer;  
}
.auto_list li{
  padding:12px;  
}
</style>
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
  <div class="row">
  <div class="col-lg-12">
	<div class="col-lg-6">
  <div class="col-lg-3">
	<input type="text" name="filter_voucher_no" id="filter_voucher_no"  class="input-sm form-control" placeholder="Voucher No">
	</div>
  <div class="col-lg-3">
	<input name="filter_transaction_date" id="filter_transaction_date"  class="input-sm  datepicker-input form-control" size="16" type="text"  data-date-format="dd-mm-yyyy"  placeholder="Transaction Date">
	</div>
  </div>

  <div class="col-lg-6">
  <div class="col-lg-3">
  <select name="from_branch" id="from_branch" class="input-sm form-control input-s-sm inline v-middle">
			<option value="0">From Branch</option>
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
  <div class="col-lg-3">
	<select name="to_branch" class="input-sm form-control input-s-sm inline v-middle">
			<option value="0">To Branch</option>
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
  <div class="col-lg-3">
  <input type="text" name="delivery_no" id="delivery_no"  class="input-sm form-control" placeholder="Delivery No">
	</div>
  <div class="col-lg-1" style="margin-right:15px;">
	<button class="btn btn-sm btn-info" type="submit" id="filter">Find</button>
	</div>
  <div class="col-lg-1">
	<button class="btn btn-sm btn-info" value="1" name="unset_filter">New</button>
	</div>
  </div>
  </div>
  </div>
  <br>

  <div class="col-lg-12">
  <div class="col-lg-2">
	<input type="text" name="barcode" id="barcode" data-bbid=""  class="input-sm form-control" placeholder="Barcode">
  <div id="barcodeList"></div> 
	</div>
  <div class="col-lg-4">
	<input type="text" name="product_name" id="product_name" data-pbid=""  class="input-sm form-control" placeholder="Product Name">
	</div>
  <div class="col-lg-1">
	<input type="text" name="quantity" id="quantity"  class="input-sm form-control" placeholder="Qty">
	</div>
  <div class="col-lg-1">
	<input type="text" name="unit" id="unit"  class="input-sm form-control" placeholder="Unit">
	</div>
  <div class="col-lg-2">
	<input type="text" name="sales_rate" id="sales_rate"  class="input-sm form-control" placeholder="Sales Rate">
	</div>
  <div class="col-lg-1">
	<input type="text" name="tax" id="tax"  class="input-sm form-control" placeholder="Tax">
	</div>
  <div class="col-lg-1">
  <button class="btn btn-sm btn-info" type="submit" id="add">Add</button>
	</div>
  </div>


					<!-- <div class="col-lg-4">
						<div class="input-group">	
							<span class="input-group-btn">
								<button class="btn btn-sm btn-default" type="submit" id="filter">Search</button>
								<button class="btn btn-sm btn-default" value="1" name="unset_filter">Clear</button>
							</span>
						</div>
					</div>					
					<div class="col-lg-2-4"></div> -->
					</form>	
  <!-- <div class="col-lg-1">
	   <a class="btn btn-sm btn-info btn-export"  href="?export=1&<?=http_build_query($_GET, '', '&')?>">Export</a>
	</div>							 -->
	</div>
  <div id="alert_message"></div>
                <div class="table-responsive">
                  <table id="stock_data" class="table table-bordered table-striped" >
                    <thead>
                      <tr>
                        <th width="20%">Invoice Date</th>
                        <th width="25%">Invoice No</th>
                        <th width="25%">Gross Amount</th>
                        <th width="15%">Discount</th>
                        <th width="15%">Net Amount</th>
                        <th></th>
                      </tr>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
                </div>
              </section>
<div class="col-lg-2">
<a href="create_excel.php" class="btn btn-default btn-xs">Excel Sample</a>
</div>
<div class="form-group">
<div class="col-sm-4">
<!-- <form enctype="multipart/form-data" method="post" role="form" action="import_excel.php"> -->
<form id="submitForm">
        <input type="file" name="import_file" id="file" class="filestyle" data-icon="false" data-classbutton="btn btn-default" data-classinput="form-control inline input-s"  style="position: fixed; left: -500px;" >
   
    <!-- <button type="submit" class="btn btn-info" name="import_file_btn">Import</button> -->
</form>
<div class="progress progress-sm m-t-sm">
  <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
</div>
</div>

</div>



<?php  include_once('footer.php'); ?>
<script>
 $(document).ready(function(){

  fill_datatable();
  
  function fill_datatable(filter_voucher_no = '', filter_transaction_date = '')
  {
   var dataTable = $('#stock_data').DataTable({
    "processing" : true,
    "serverSide" : true,
    "order" : [],
     sDom: 'lrtip',
    "searching" : false,
    "ajax" : {
     url:"fetch.php",
     type:"POST",
     data:{
      filter_voucher_no:filter_voucher_no, filter_transaction_date:filter_transaction_date
     }
    }
   });
  }
  
  $('#filter').click(function(){
   var filter_voucher_no = $('#filter_voucher_no').val();
   var filter_transaction_date = $('#filter_transaction_date').val();
   if(filter_voucher_no != '' && filter_transaction_date != '')
   {
    $('#stock_data').DataTable().destroy();
    fill_datatable(filter_voucher_no, filter_transaction_date);
   }
   else
   {
    alert('Select Both filter option');
    $('#stock_data').DataTable().destroy();
    fill_datatable();
   }
  });
  
  
 });



function update_data(id, column_name, value)
  {
   $.ajax({
    url:"update.php",
    method:"POST",
    data:{id:id, column_name:column_name, value:value},
    success:function(data)
    {
     $('#alert_message').html('<div class="alert alert-success">'+data+'</div>');
     $('#stock_data').DataTable().destroy();
     fill_datatable();
    }
   });
   setInterval(function(){
    $('#alert_message').html('');
   }, 5000);
  }

  $(document).on('blur', '.update', function(){
   var id = $(this).data("id");
   var column_name = $(this).data("column");
   var value = $(this).text();
   update_data(id, column_name, value);
  });



  $(document).ready(function(){
   $("#submitForm").on("change", function(){
      var formData = new FormData(this);
      $.ajax({
         url  : "upload_excel.php",
         type : "POST",
         cache: false,
         contentType : false, // you can also use multipart/form-data replace of false
         processData: false,
         data: formData,
         async: true,
         xhr: function () {
          var xhr = new window.XMLHttpRequest();
      //Upload Progress
          xhr.upload.addEventListener("progress", function (evt) {
         if (evt.lengthComputable) {
          var percentComplete = (evt.loaded / evt.total) * 100; $('div.progress > div.progress-bar').css({ "width": percentComplete + "%" }); } }, false);
 
      //Download progress
          xhr.addEventListener("progress", function (evt)
          {
          if (evt.lengthComputable)
            { var percentComplete = (evt.loaded / evt.total) *100;
          $("div.progress > div.progress-bar").css({ "width": percentComplete + "%" }); } },
          false);
          return xhr;
          },
          success:function(response){
          $('.progress').fadeIn('fast').delay(1000).fadeOut('fast');
         $('#stock_data').DataTable().destroy();
         fill_datatable(); 
         }
      });
   });

});
$(document).ready(function(){ 
$('#from_branch').change(function(){
  var val = $(this).val();
  var val = $(this).val();
  $('#barcode').attr('data-bbid', val);
  $('#product_name').attr('data-pbid', val);
      }); 
 });  


$(document).ready(function(){  
      $('#barcode').keyup(function(){  
           var query  = $(this).val();
           var branch = $('#barcode').attr('data-bbid');  
           if(query != '')  
           {  
                $.ajax({  
                     url:"barcode_search.php",  
                     method:"POST",  
                     data:{query:query,branch:branch},  
                     success:function(data)  
                     {  
                          $('#barcodeList').fadeIn();  
                          $('#barcodeList').html(data);  
                     }  
                });  
           }  
      });  
      $(document).on('click', 'li', function(){  
          //  $('#barcode').val($(this).text());  
          //  $('#barcodeList').fadeOut();  
      });  
 });  


 
</script>