<?php
include_once("check_login.php");
include_once('class/common_settings.php');
include_once("class/Crud.php");
include_once("class/Report.php");
include_once("class/StockTransfer.php"); 
include_once("class/ProcessExcel.php");

if(isset($_GET['searchVoucher']) && (int)$_GET['searchVoucher'] == 1){
  if(strtotime($_POST['from_date']) == 0){
    $_POST['from_date'] = date('Y-m-d');
  }
  if(strtotime($_POST['to_date']) == 0){
    $_POST['to_date'] = date('Y-m-d');
  }
  $search = new StockTransfer($_POST, $_GET);
  echo $search->searchVoucher();
  exit;
}

if(isset($_GET['save_stock']) && $_GET['save_stock'] == 1){
  $stock = new StockTransfer($_POST);

  if((int)$_POST['stock_master_id'] > 0){
    $stock->setStockMasterId((int)$_POST['stock_master_id']);
  }

  if((int)$_POST['voucher_no'] > 0){  
    $stock->setVoucherNo((int)$_POST['voucher_no']);
  }
  if((int)$_POST['detail_id'] > 0){
    $stock->setStockDetailId((int)$_POST['detail_id']);
  }
  echo $stock->saveStock();
  exit;
}

if(isset($_GET['get_stock_detail']) && (int)$_GET['get_stock_detail'] == 1){
  $stock = new StockTransfer(null, $_GET);
  $stock->setStockDetailId((int)$_GET['stock_detail_id']);
  echo json_encode($stock->getStockDetails());
  exit;
}

if(isset($_GET['save_master']) && (int)$_GET['save_master'] == 1){
  $stock = new StockTransfer();
  if((int)$_GET['stock_master_id'] > 0){
    $stock->setStockMasterId((int)$_GET['stock_master_id']);
    echo $stock->saveStockMaster();
  }  
  exit;
}

if(isset($_GET['getDatatable']) && $_GET['getDatatable'] == 1){
  $stock = new StockTransfer($_POST, $_GET);
  echo $stock->getDatatable();
  exit;
}

if(isset($_GET['import_stock']) && (int)$_GET['import_stock'] == 1){
  if($_FILES['import_file']['error'] == 0){
    $import = new ProcessExcel($_POST, $_FILES);
    $import->setFileFieldName('import_file');
    $import->setAllowedExtension(['xls','csv','xlsx']);
    $rowdata = $import->processData();
    $stock = new StockTransfer($_POST);
    echo $stock->ProcessExcelData($rowdata['data']);
    exit;   
  } 
}

$crud = new Crud();

if(isset($_GET['getBarcode'])){

  if((int)$_GET['branchcode'] > 0){
    $_GET['search'] = isset($_GET['search'])? $crud->escape_string($_GET['search']):'';
    $barcode = $crud->getData("SELECT pd.barcode,pd.Product_Detail_Id,pd.product_id,pd.Product_Code,pd.Product_Detail_Name,pm.Base_Unit_Id,u.Unit_Name,pd.Stock,pd.Retail_Rate 
    FROM product_detail pd 
    LEFT JOIN product_master pm ON pd.product_id=pm.product_id 
    LEFT JOIN unit u ON pm.Base_Unit_Id=u.Unit_Id
    WHERE pd.Barcode LIKE '".$_GET['search']."%' and pd.Branch_Id = ".(int)$_GET['branchcode']." order by pd.barcode", 10);
    foreach($barcode as $k => $data){
      $result['results'][] = array( 'id' => $data['barcode'], 
                                    'text' => $data['barcode'], 
                                    'product_id' => $data['product_id'], 
                                    'product_detail_id' => $data['Product_Detail_Id'], 
                                    'product_code' => $data['Product_Code'], 
                                    'product_name' => $data['Product_Detail_Name'].' ( '.$crud->num_format($data['Stock']).' )', 
                                    'unit_id' => $data['Base_Unit_Id'],
                                    'stock' => $crud->num_format($data['Stock']),
                                    'retail_rate' => $crud->num_format($data['Retail_Rate']),
                                  );
    }
  }
  if(empty($result)){
    $result['results'][] = ['id' => 0, 'text' => 'No match'];
  }
  echo json_encode($result); 
  exit;
}

if(isset($_GET['getProduct'])){

  if((int)$_GET['branchcode'] > 0){
    $_GET['search'] = isset($_GET['search'])? $crud->escape_string($_GET['search']):'';
    $product = $crud->getData("SELECT pd.barcode,pd.product_id,pd.Product_Detail_Id,pd.Product_Code,pd.Product_Detail_Name,pm.Base_Unit_Id,u.Unit_Name,pd.Stock,pd.Retail_Rate
    FROM product_detail pd LEFT JOIN product_master pm ON pd.product_id=pm.product_id
    LEFT JOIN unit u ON pm.Base_Unit_Id=u.Unit_Id 
    WHERE pm.Product_name LIKE '".$_GET['search']."%' and pd.Branch_Id = ".(int)$_GET['branchcode']." order by pm.Product_name", 10);
    foreach($product as $k => $data){
      $result['results'][] = array( 'id' => $data['Product_Detail_Id'], 
                                    'text' => $data['Product_Detail_Name'].' ( '.$crud->num_format($data['Stock']).' )',
                                    'product_id' => $data['product_id'],
                                    'barcode' => $data['barcode'], 
                                    'product_code' => $data['Product_Code'],
                                    'unit_id' => $data['Base_Unit_Id'],
                                    'stock' => $crud->num_format($data['Stock']),
                                    'retail_rate' => $crud->num_format($data['Retail_Rate']),
                                  );
    }
  }
  if(empty($result)){
    $result['results'][] = ['id' => 0, 'text' => 'No match'];
  }
  echo json_encode($result); 
  exit;
}

include_once('menu.php');

$branch = $crud->getData("select Branch_Id,Branch_Name from branch order by Branch_Name");
$units = $crud->getData("SELECT Unit_Id,Unit_Name FROM unit ORDER BY Unit_Name");
$taxes = $crud->getData("SELECT Tax_Id,Tax_Percentage FROM tax ORDER BY Tax_Percentage");
?>
<style>
  .auto_list{
  background-color:#eee;  
  cursor:pointer;  
}
.auto_list li{
  padding:12px;  
}
#search_table tbody .even:hover {
   /*background-color:#71d1eb !important;*/
   cursor: pointer;
}
#search_table tbody tr.odd:hover {
  /*background-color:#71d1eb !important;*/
   cursor: pointer;
} 

</style>
<div class="m-b-md">
	<h3 class="m-b-none">Stock Transfer</h3>
</div>

<section class="panel panel-default">
                <header class="panel-heading">
                Stock Transfer 
                  <i class="fa fa-info-sign text-muted" data-toggle="tooltip" data-placement="bottom" data-title="ajax to load the data."></i> 
                </header>
                <div class="row wrapper">
	
	<form method="get">
  <div class="row">
  <div class="col-lg-12">
	<div class="col-lg-6">
  <div class="col-lg-3">
	<input type="text" name="voucher_no" id="voucher_no" readonly="readonly"  class="input-sm form-control" placeholder="Voucher No">
	</div>
  <div class="col-lg-3">
	<input name="transaction_date" id="transaction_date" data-cdate="<?=date('d-m-Y')?>" value="<?=date('d-m-Y')?>"  class="input-sm form-control" type="text"  placeholder="Transaction Date">
	</div>
  </div>

  <div class="col-lg-6">
  <div class="col-lg-3">
  <select name="from_branch" id="from_branch" class="input-sm form-control input-s-sm inline v-middle">
			<option value="0">From Branch</option>
				<?php foreach ($branch as $k => $com) {
					echo '<option value="'.$com['Branch_Id'].'">'.$com['Branch_Name'].'</option>';
					}
				?>				
			</select>
	</div>
  <div class="col-lg-3">
	<select name="to_branch" id="to_branch" class="input-sm form-control input-s-sm inline v-middle">
			<option value="0">To Branch</option>
				<?php foreach ($branch as $k => $com) {
					echo '<option value="'.$com['Branch_Id'].'">'.$com['Branch_Name'].'</option>';
					}
				?>				
			</select>
	</div>
  <div class="col-lg-3">
  <input type="text" name="delivery_no" id="delivery_no"  class="input-sm form-control" placeholder="Delivery No">
	</div> 
  <div class="col-lg-1">
	<button class="btn btn-sm btn-info" value="1" id="unset_filter">New</button>
	</div>
  </div>
  </div>
  </div>
  <br>

  <div class="col-lg-12">
  <div class="col-lg-2">

  <select class="form-control" id="barcode" name="barcode">
    <option value="0">Choose Barcode</option>
  </select>

  <div id="barcodeList"></div>
	</div>
  <div class="col-lg-4">

    <select class="form-control" id="product_detail_id" name="product_name">
      <option value="0">Choose Product</option>
    </select>

	</div>
  <div class="col-lg-1">
	<input type="number" name="quantity" id="quantity"  class="input-sm form-control" placeholder="Qty">
	</div>
  <div class="col-lg-1">
  <select name="unit" id="unit" class="form-control input-sm">
  <option value="0">Unit</option>
  <?
    foreach($units as $k => $unit){
      echo "<option value='{$unit['Unit_Id']}'>{$unit["Unit_Name"]}</option>";
    }
  ?>
  </select>	
	</div>
  <div class="col-lg-2">
	<input type="text" name="sales_rate" id="sales_rate"  class="input-sm form-control" placeholder="Sales Rate">
	</div>
  <div class="col-lg-1">
	<select name="tax" id="tax" class="form-control input-sm">
  <option value="0">Tax</option>
  <?
    foreach($taxes as $k => $tax){
      echo "<option value='{$tax['Tax_Percentage']}'>{$tax["Tax_Percentage"]}</option>";
    }
  ?>
  </select>	
	</div>
  <div class="col-lg-1">
  <button class="btn btn-sm btn-info" data-detail_id="0" id="add">Add</button>
	</div>
  </div>
				
					</form>	
	</div>
  <div id="alert_message"></div>
                <div class="table-responsive">
                  <table id="stock_data" class="table table-bordered table-striped" >
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>PRODUCT CODE</th>
                        <th>BARCODE</th>
                        <th>PRODUCT NAME</th>
                        <th>ORDER QTY</th>
                        <th>TRANSFER QTY</th>
                        <th>UNIT</th>
                        <th>GROSS VALUE</th>
                        <th>TAX AMOUNT</th>
                        <th>TOTAL</th>
                        <th>EDIT/DELETE</th>                        
                      </tr>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
                </div>
<br>
 <div class="row">
  <div class="col-lg-12">
  <div class="col-lg-7"> 
    <div class="col-lg-3">
        <a href="templates/StockTransferTemplate.xlsx" class="">Excel Sample</a>
    </div>    
  </div>

  <div class="form-inline">
  <div class="form-group">
              <!-- <form enctype="multipart/form-data" method="post" role="form" action="import_excel.php"> -->
        <form id="submitForm">
            <input type="file" name="import_file" id="file" class="filestyle" data-icon="false" data-classbutton="btn btn-info" data-classinput="form-control inline input-s hidden"  style="position: fixed; left: -500px;" >
        </form>
        <div class="progress progress-sm m-t-sm hidden">
          <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
   </div> 
    <div class="form-group">
      <a class="btn btn-info" href="#search_modal" data-toggle="modal">Search</a>     
    </div>
    <div class="form-group">
      <button class="btn btn-info" id="save" data-master_id="0">Save</button>
    </div>
  </div>
  </div>
 </div>
 <br>
</section>

<!-- Modal -->
<div class="modal fade" id="search_modal">
    <div class="modal-dialog">
      <div class="modal-content" style="width: 800px;">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">Search Stock</h4>
        </div>
        <div class="modal-body">
        <div class="panel-body">
                  <form class="form-inline" role="form">
                    <div class="form-group">                     
                      <input type="text" id="from_date" value="<?=date('d-m-Y')?>"  class="input-sm  form-control" placeholder="From Date">
                    </div>
                    <div class="form-group">
                      <input type="text" id="to_date" value="<?=date('d-m-Y')?>"  class="input-sm form-control"  placeholder="To Date">
                    </div>
                    <div class="form-group">
                      <input type="text" class="input-sm form-control" id="srch_voucher_no" placeholder="Voucher No">
                    </div>
                    <div class="form-group">
                      <select id="srch_from_branch" class="inut-sm form-control">
                        <option value="0">From branch</option>
                        <?php 
                            foreach ($branch as $k => $com) {                                                    					
                              echo '<option value="'.$com['Branch_Id'].'">'.$com['Branch_Name'].'</option>';
                            }
                         ?>
                      </select>
                    </div>
                    <div class="form-group">
                    <select id="srch_to_branch" class="inut-sm form-control">
                        <option value="0">To branch</option>
                        <?php 
                            foreach ($branch as $k => $com) {                                                    					
                              echo '<option value="'.$com['Branch_Id'].'">'.$com['Branch_Name'].'</option>';
                            }
                         ?>
                      </select>
                    </div>
                    <div class="form-group">
                      <button type="button" id="search" class="input-sm btn btn-info">Search</button>
                      <button type="button" id="close_modal" class="input-sm btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                  </form>                              
         </div>
         <div class="row">
                  <div class="col-lg-12">                  
                  <table id='search_table' class='table table-bordered table-striped'>
                    <thead>
                      <tr>
                        <th>Voucher No</th>
                        <th>Voucher Date</th>
                        <th>From Branch</th>
                        <th>To Branch</th>                        
                      </tr>
                    </thead>
                  </table>                
                  </div>
                  </div> 
         
          
        </div>
        <div class="modal-footer hidden">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>          
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div>

<?php  include_once('footer.php'); ?>
<script>
var stock = product_id = stock_master_id = 0;
var dataTable;

var emptyTable = function(){
  dataTable = $('#stock_data').DataTable({"ordering": false,"searching" : false,"info":false,"bLengthChange": false,
                              fnFooterCallback: function(row, data, start, end, display) {               
                                if(!$(this).find('tfoot').length){
                                  var footer = $(this).append(`<tfoot style="background-color: #ddd;"><tr><th colspan="5"></th>
                                                                            <th><b>Qty</b></th>
                                                                            <th><b>Gross Amount</b></th>
                                                                            <th><b>Disc</b></th>
                                                                            <th><b>Net Amount</b></th>
                                                                            <th><b>Tax Amount</b></th>
                                                                            <th><b>Total Amount</b></th>                                                                            
                                                                            </tr></tfoot>`);
                                }
                                  //$( api.column( 5 ).footer() ).html('<b>Qty</b>');
                                  // this.api().columns().every(function () {
                                  //   var column = this;
                                  //   $(footer).append('<th><input type="text" style="width:100%;"></th>');
                                  // });
                                }  
  
                            });
}

emptyTable();

var fill_datatable = function(voucher_no = 0){
   dataTable.destroy(); 
   dataTable = $('#stock_data').DataTable({
                                "ordering": false,
                                "searching" : false,
                                "info":false,
                                "bLengthChange": false,
                                "processing" : true,
                                "serverSide" : true,                                
                                sDom: 'lrtip',
                                "searching" : false,
                                  "ajax" : {
                                     url:"stock_transfer.php?getDatatable=1",
                                     type:"POST",
                                     data:{
                                      voucher_no:voucher_no,                                      
                                     }
                                  },
                              fnFooterCallback: function(row, data, start, end, display) {
                                if(data.length > 0){
                                  var api = this.api();
                                  var total = api.column(5)
                                                  .data()
                                                  .reduce( function (a, b) {
                                                      return parseFloat(a) + parseFloat(b)
                                                  }, 0 );                                  
                                      var api = this.api();
                                      var footer = $(this).find('tfoot').html(`<tr><th colspan="5"></th>
                                                                                <th><b>Qty : `+total+`</b></th>
                                                                                <th><b>Gross Amount : `+data[0].tot_gross_amt+`</b></th>
                                                                                <th><b>Disc : `+data[0].tot_discount+`</b></th>
                                                                                <th><b>Net Amount : `+data[0].tot_net_amt+`</b></th>
                                                                                <th><b>Tax Amount : `+data[0].tot_tax_amt+` </b></th>
                                                                                <th><b>Total Amount : `+data[0].tot_amt+` </b></th>                                                                            
                                                                                </tr>`);                                  
                                    }else{
                                      $(this).find('tfoot').html(`<tr><th colspan="5"></th>
                                                                            <th><b>Qty</b></th>
                                                                            <th><b>Gross Amount</b></th>
                                                                            <th><b>Disc</b></th>
                                                                            <th><b>Net Amount</b></th>
                                                                            <th><b>Tax Amount</b></th>
                                                                            <th><b>Total Amount</b></th>                                                                            
                                                                            </tr>`);
                                    }
                              }                          
                               });
  } 


  $(document).ready(function(){

    $('#submitForm').find('.bootstrap-filestyle').find('label > span').text('Import');
    var import_file = null;
    $('input[name="import_file"]').change(function(){
       import_file = this.files[0];      
    });

    var clearInputFile = function(){
      $('input[name="import_file"]').val(null);
      $('#submitForm').find('input').val('');
    }

    var checkFileExtension = function(){
      var ext = import_file.name.split('.').pop().toLowerCase();
      if ($.inArray(ext, ['xls','csv','xlsx']) == -1){
        alert('Allowed File type xls, csv, xlsx');
        clearInputFile();
        return false;
      }

      if($('#from_branch').val() == 0 || $('#to_branch').val() == 0){
        alert("Please choose From/To Branch");
        clearInputFile();
        return false;
      }else if( parseInt($('#from_branch').val()) == parseInt($('#to_branch').val())){
        alert('From and To Branch should not be equal');
        clearInputFile();
        return false;
      }

      return true;
    
    }

   $("#submitForm").on("change", function(e){     
      if(checkFileExtension() == false){      
        return false;       
        e.preventDefault();       
      }
      $('.progress').removeClass('hidden');
      var formData = new FormData(this);
      formData.append('transaction_date', $('#transaction_date').val());
      formData.append('from_branch', $('#from_branch').val());
      formData.append('to_branch', $('#to_branch').val());
      formData.append('delivery_no', $('#delivery_no').val());      
      $.ajax({
         url  : "stock_transfer.php?import_stock=1",
         type : "POST",
         cache: false,
         contentType : false, // you can also use multipart/form-data replace of false
         processData: false,
         data: formData,
         dataType:'json',
         async: true,
         xhr: function () {
            var xhr = new window.XMLHttpRequest();
        
            xhr.upload.addEventListener("progress", function (evt) {
              if (evt.lengthComputable) {
                var percentComplete = (evt.loaded / evt.total) * 100; $('div.progress > div.progress-bar').css({ "width": percentComplete + "%" }); 
              } 
            }, false);

            xhr.addEventListener("progress", function (evt){
                if (evt.lengthComputable)
                  { var percentComplete = (evt.loaded / evt.total) *100;
                $("div.progress > div.progress-bar").css({ "width": percentComplete + "%" }); 
              } 
            },false);
            return xhr;
          },

          success:function(res){      
            $('.progress').addClass('hidden'); 
            clearInputFile();
            $('.progress').fadeIn('fast').delay(1000).fadeOut('fast');
            fill_datatable(res.voucher_no);
            stock_master_id = res.stock_master_id;
            $('#voucher_no').val(res.voucher_no); 
            $('#save').data('master_id',res.stock_master_id);
            $('#add').data('detail_id',0);            
         }
      });
   });

});


$('#from_branch').change(function(){
  $('select[name="barcode"]').val(0).trigger("change");
  $('select[name="product_name"]').val(0).trigger("change");
  $('select[name="unit"]').val(0);
  $('#sales_rate').val('');
  stock = product_id = 0;
});

$(document).ready(function () {
    $('select[name="barcode"]').select2({
          ajax: {
            url: 'stock_transfer.php?getBarcode=1',
            dataType: 'json',
            data: function (params) {
              var query = {
                search: params.term,
                branchcode: $('#from_branch').val(),
              }
              // Query parameters will be ?search=[term]&type=public
              return query;
            }
          }
     });

    $('select[name="barcode"]').on('select2:select', function(e) {
      var data = e.params.data; 
      var newOption = new Option(data.product_name, data.product_detail_id, true, true);
      $('select[name="product_name"]').append(newOption).trigger('change');
      $('select[name="unit"]').val(data.unit_id);
      $('#sales_rate').val(data.retail_rate);
      stock = data.stock;
      product_id = data.product_id;
    });

    $('select[name="product_name"]' ).select2({
      ajax: {
            url: 'stock_transfer.php?getProduct=1',
            dataType: 'json',
            data: function (params) {
              var query = {
                search: params.term,
                branchcode: $('#from_branch').val(),
              }
              // Query parameters will be ?search=[term]&type=public
              return query;
            }
          }
    });

    $('select[name="product_name"]').on('select2:select', function(e) {
      var data = e.params.data;
      var newOption = new Option(data.barcode, data.barcode, true, true);
      $('select[name="barcode"]').append(newOption).trigger('change');
      $('select[name="unit"]').val(data.unit_id);
      $('#sales_rate').val(data.retail_rate);
      stock = data.stock;
      product_id = data.product_id;
    });
});

$('#add').on('click',function(e){
  e.preventDefault();
  var detail_id = parseInt($(this).data('detail_id'));
  if($('#from_branch').val() == 0 || $('#to_branch').val() == 0){
    alert("Please choose From/To Branch");
  }else if( parseInt($('#from_branch').val()) == parseInt($('#to_branch').val())){
        alert('From and To Branch should not be equal');
        return false;
  }else{
    
    var postdata = {
      detail_id: detail_id,
      transaction_date: $('#transaction_date').val(),
      from_branch: $('#from_branch').val(),
      to_branch: $('#to_branch').val(),
      delivery_no: $('#delivery_no').val(),
      barcode: $('#barcode').val(),
      product_detail_id: $('#product_detail_id').val(),
      product_id: product_id,
      quantity: $('#quantity').val(),
      unit: $('#unit').val(),
      sales_rate: $('#sales_rate').val(),
      tax: $('#tax').val(),
      stock_master_id: stock_master_id,
      voucher_no: $('#voucher_no').val(),

    };

    if(!checkStock(postdata.quantity)){    
      return false;
    }

    $.ajax({
      url:'stock_transfer.php?save_stock=1',
      dataType: 'json',
      data: postdata,
      method: 'post',
    }).done(function(res){
      resetForm();
      fill_datatable(res.voucher_no);
      stock_master_id = res.stock_master_id;
      $('#voucher_no').val(res.voucher_no); 
      $('#save').data('master_id',res.stock_master_id);
      $('#add').data('detail_id',0);
    });

  }
});


$('#quantity').on('keyup',function(){
  let qty = $(this).val();
  checkStock(qty);
});

var checkStock = function(qty){
  if(stock == 0){
    alert('Choose a product first!');
    $('#quantity').val('');
    return false;
  } 
  
  if((parseFloat(qty) > parseFloat(stock)) || !qty ){
    alert('There is only '+stock+' stock available! Please check the quantity entered');
    $('#quantity').val('');
    return false;
  }
  return true;
}

var resetForm = function(all = 0){
  product_id = stock = 0;
  $('select[name="barcode"]').val(0).trigger("change");
  $('select[name="product_name"]').val(0).trigger("change");  
  $('#quantity').val('');
  $('#unit').val(0); 
  $('#sales_rate').val('');
  $('#add').data('detail_id',0);

  if(all == 1){
    stock_master_id = 0;
    $('#transaction_date').val($('#transaction_date').data('cdate'));
    $('#from_branch').val(0);
    $('#to_branch').val(0);
    $('#delivery_no').val('');
    $('#tax').val(0);
    $('#voucher_no').val('');
    $('#save').data('master_id', 0);   
    $('#save').removeAttr('disabled');
    $('#add').removeAttr('disabled');
    fill_datatable(0);
  }

}

$('#unset_filter').on('click', function(e){
  e.preventDefault();
  resetForm(1);
});


$('#save').on('click', function(e){
  e.preventDefault();
  var stock_master_id = $(this).data('master_id');
  if(parseInt(stock_master_id) > 0){
    $.ajax({
      url:'stock_transfer.php?save_master=1&stock_master_id='+stock_master_id+'',
      dataType:'json',
    }).done(function(res){   
      $('#save').attr("disabled", 'disabled');   
      $('#add').attr("disabled", 'disabled');   
      fill_datatable(res.voucher_no);
      alert('Stock Saved');
    });
  }else{
    alert('No stock to save');
  }  
});

$('body').on('click', '.edit', function(){
  
  var id = $(this).data('id');
  $.ajax({
    url:'stock_transfer.php?get_stock_detail=1&stock_detail_id='+id+'',
    dataType:'json',
  }).done(function(res){
    $('#add').data('detail_id',res.StockTransfer_Detail_Id);   
    var newOption = new Option(res.product_name, res.Product_Detail_Id, true, true);
    $('select[name="product_name"]').append(newOption).trigger('change');
    var newOption2 = new Option(res.Barcode, res.Barcode, true, true);
    $('select[name="barcode"]').append(newOption2).trigger('change');
    $('#quantity').val(parseFloat(res.Quantity));
    $('#unit').val(res.Base_Unit_Id); 
    $('#sales_rate').val(parseFloat(res.Sales_Rate));
    product_id = parseInt(res.Product_Id);
    stock_master_id = parseInt(res.StockTransfer_Master_Id);
    stock = res.stock;
  });
});

$('body').on('click', '.delete', function(){
  console.log($(this).data('id'));
});

var srchDataTable;
var searchDataTable = function(params){
  if ($.fn.DataTable.isDataTable('#search_table') ) {
    srchDataTable.destroy();
  }
  
  srchDataTable =  $('#search_table').DataTable({
      "ordering": false, "searching": false, "info": false, "bLengthChange": false,
      'processing': true,
      'serverSide': true,
      'serverMethod': 'post',
      'ajax': {
          'url':'stock_transfer.php?searchVoucher=1',
          data:params,
      },
      'columns': [
         { data: 'voucher_no' },
         { data: 'voucher_date' },
         { data: 'from_branch' },
         { data: 'to_branch' },        
      ]
   });
}

$('#search_modal').on('shown.bs.modal', function (e) { 
  $('#from_date').val('<?=date('d-m-Y')?>');
  $('#to_date').val('<?=date('d-m-Y')?>');
  var param = {
    from_date:$('#from_date').val(),
    to_date:$('#to_date').val(),
    voucher_no:0,
    from_branch:'',
    to_branch:''
  }
  searchDataTable(param);
});

$('#search').on('click',function(e){
  e.preventDefault();  
  var param = {
    from_date: $('#from_date').val(),
    to_date: $('#to_date').val(),
    voucher_no: parseInt($('#srch_voucher_no').val()),
    from_branch: parseInt($('#srch_from_branch').val()),
    to_branch: parseInt($('#srch_to_branch').val()),
  }
  searchDataTable(param);
});

$('#from_date').datepicker({    
  format: 'dd-mm-yyyy',
  zIndex: 1050,
  autoHide:true,
});
  
$('#to_date').datepicker({    
  format: 'dd-mm-yyyy',
  zIndex: 1050,
  autoHide:true,
});

$('#transaction_date').datepicker({    
  format: 'dd-mm-yyyy', 
  autoHide:true,
});

$('body').on('click', '#search_table tbody tr', function (e) {

  var row = srchDataTable.row($(this)).data();
  fill_datatable(row.voucher_no);
    $('#from_branch').val(row.from_branch_id);
    $('#to_branch').val(row.to_branch_id);
    $('#delivery_no').val(row.delivery_no);
    $('#transaction_date').val(row.voucher_date);
    $('#voucher_no').val(row.voucher_no);
    if(parseInt(row.is_saved) == 1){
      $('#save').attr("disabled", 'disabled');   
      $('#add').attr("disabled", 'disabled'); 
    }else{
      $('#save').removeAttr("disabled");   
      $('#add').removeAttr("disabled", 'disabled'); 
      stock_master_id = row.master_id;
      $('#save').data('master_id',row.master_id);

    }
    $('#search_modal').modal("hide");
    $('.modal-backdrop').remove();
  
});

 
</script>