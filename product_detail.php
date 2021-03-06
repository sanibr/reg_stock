<?php
include_once("check_login.php");
include_once('class/Crud.php');
include_once('class/ProductDetails.php');


if(isset($_GET['get_product_list']) && $_GET['get_product_list'] == 1){
  $list = new ProductDetails($_POST, $_GET);
  echo $list->getProductList();
  exit;
}

include_once('menu.php');
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
	<h3 class="m-b-none">Product List</h3>
</div>

<section class="panel panel-default">
                <header class="panel-heading">
                Product List
                  <i class="fa fa-info-sign text-muted" data-toggle="tooltip" data-placement="bottom" data-title="ajax to load the data."></i> 
                </header>
 <div class="row wrapper">
  <div class="row">    
    <div class="col-lg-12">    
    <div class="col-lg-3">
       <input type="text" id="product_name" placeholder="Product Name" class="form-control"/>      
    </div>  
    <div class="col-lg-2">
       <input type="text" id="bar_code"  placeholder="Barcode" class="form-control"/>      
    </div> 
    <div class="col-lg-2">
       <input type="text" id="product_code"  placeholder="Product Code" class="form-control"/>      
    </div> 
    <div class="col-lg-2">
       <select id="brand" class="form-control">
         <option value="0">Brand</option>
       </select>     
    </div> 
    <div class="col-lg-2">
    <select id="category" class="form-control">
         <option value="0">Category</option>
    </select> 
    </div> 
    <div class="col-lg-1">
       <buttopn class="btn btn-info" id="search">Search</buttopn>
    </div>
    </div>  
    </div> 
	</div>

  <div class="row wrapper">
  <div class="row">    
    <div class="col-lg-12">
    <div class="col-lg-3">
    </div> 
 
    <div class="col-lg-2">
    <select class="form-control">
         <option value="0">Color</option>
    </select>    
    </div> 
    <div class="col-lg-2">
       <select class="form-control">
         <option value="0">Size</option>
       </select>     
    </div> 
    <div class="col-lg-2">
    <select class="form-control">
         <option value="0">Branch</option>
    </select> 
    </div>  
    </div>  
    </div> 
	</div>


  <div id="alert_message"></div>
  <div class="row">
                <div class="col-lg-12">    
                <div class="table-responsive">
                  <table style="width:100%" id="product_list" class="table table-bordered table-striped" >
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>CODE</th>
                        <th>BARCODE</th>
                        <th>PRODUCT NAME</th>
                        <th>STOCK</th>
                        <th>P.RATE</th>
                        <th>P.COST</th>
                        <th>W.RATE</th>
                        <th>R.RATE</th>
                        <th>MRP</th>
                        <th>FROM</th>
                        <th>SP.RATE</th>
                        <th>TO</th>
                        <th>DISC</th>
                        <th>DISC AMT</th>
                        <th>DISC RATE</th>
                        <th>BRANCH</th>                       
                      </tr>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
                </div>
                </div>
                </div>
<br>
 
</section>

<?php  include_once('footer.php'); ?>
<script>

var productList;
var ProductDataTable = function(params = {}){
  if ($.fn.DataTable.isDataTable('#product_list') ) {
    productList.destroy();
  }
  
  productList =  $('#product_list').DataTable({
      "ordering": false, "searching": false, "info": false, "bLengthChange": false,
      'processing': true,
      'serverSide': true,
      'serverMethod': 'post',      
      'ajax': {
          'url':'product_detail.php?get_product_list=1',
           data:params,
      },
      'columns': [
         { data: 'product_id' },
         { data: 'code' },
         { data: 'barcode' },
         { data: 'product_name' },      
         { data: 'stock' },      
         { data: 'p_rate' },      
         { data: 'p_cost' },     
         { data: 'w_rate' },      
         { data: 'r_rate' },      
         { data: 'mrp' },      
         { data: 'from' },      
         { data: 'sp_rate' },      
         { data: 'to' },      
         { data: 'disc' },      
         { data: 'disc_amt' },      
         { data: 'disc_rate' },      
         { data: 'branch' },             
      ]
   });
}


ProductDataTable();

$('#search').on('click',function(e){
  e.preventDefault();
  let params = {
    'product_name': $('#product_name').val(),
    'bar_code': $('#bar_code').val(),
    'product_code': $('#product_code').val(),
    'brand': $('#brand').val() ,
    'category': $('#category').val()
  }

  ProductDataTable(params);
})

</script>
