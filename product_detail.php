<?php
include_once("check_login.php");
include_once('class/Crud.php');
include_once('class/ProductDetails.php');


if(isset($_GET['get_product_list']) && (int)$_GET['get_product_list'] == 1){
  $list = new ProductDetails($_POST, $_GET);
  echo $list->getProductList();
  exit;
}

if(isset($_GET['update_product']) && (int)$_GET['update_product'] == 1){
  $update = new ProductDetails($_POST, $_GET);
  echo $update->updateProduct();
  exit;
} 
if(isset($_GET['bulk_update_product']) && (int)$_GET['bulk_update_product'] == 1){
  $update = new ProductDetails($_POST, $_GET);
  echo $update->bulkUpdateProduct();
  exit;
} 

$brand = (new ProductDetails())->getTableData('product_brand');
$category = (new ProductDetails())->getTableData('product_category');
$color = (new ProductDetails())->getTableData('color');
$size = (new ProductDetails())->getTableData('size');
$branch = (new ProductDetails())->getTableData('branch');

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
    <div class="col-lg-4">
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
         <?
          foreach($brand as $k => $b){
            echo '<option value="'.$b["Brand_ID"].'">'.$b["Brand_Name"].'</option>';
          }
         ?>
       </select>     
    </div> 
    <div class="col-lg-2">
    <select id="category" class="form-control">
         <option value="0">Category</option>
         <?
          foreach($category as $k => $c){
            echo '<option value="'.$c["Category_ID"].'">'.$c["Category_Name"].'</option>';
          }
         ?>
    </select> 
    </div>   
    </div>  
    </div> 
	</div>

  <div class="row wrapper">
  <div class="row">    
    <div class="col-lg-12">  
 
    <div class="col-lg-2">
    <select id="color" class="form-control">
         <option value="0">Color</option>
         <?
          foreach($color as $k => $col){
            echo '<option value="'.$col["Color_ID"].'">'.$col["Color_Name"].'</option>';
          }
         ?>
    </select>    
    </div> 
    <div class="col-lg-2">
       <select id="size" class="form-control">
         <option value="0">Size</option>
         <?
          foreach($size as $k => $s){
            echo '<option value="'.$s["Size_ID"].'">'.$s["Size_Name"].'</option>';
          }
         ?>
       </select>     
    </div> 
    <div class="col-lg-2">
    <select id="branch" class="form-control">
         <option value="0">Branch</option>
         <?
          foreach($branch as $k => $br){
            echo '<option value="'.$br["Branch_ID"].'">'.$br["Branch_Name"].'</option>';
          }
         ?>
    </select> 
    </div>  
    <div class="col-lg-1">   
       <buttopn class="btn btn-info" id="search">Search</buttopn>
    </div>
    </div>  
    </div> 
	</div>


  <div id="alert_message"></div>
  <div class="row">
                <div class="col-lg-12">   
                <div class="table-responsive">
                  <table id="product_list" class="table table-bordered table-striped" style="width: 2000px !important" >
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
                        <th><input type="checkbox" id="check_all"/>All</th>                    
                      </tr>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
                </div>
                </div>
                </div>
<br>
 
 <div class="row">
  <div class="col-lg-12" style="margin-left: 10px;">  
  <div class="form-inline">
      <div class="form-group">    
        <input type="text" class="form-control" placeholder="Disc %" id="disc_per"/>
        <button class="btn btn-info update_all">UPDATE</button>       
      </div> 
      <div class="form-group">
        <input type="text" class="form-control" placeholder="Disc Amount" id="disc_amt"/>
        <button class="btn btn-info update_all">UPDATE</button>
      </div>
      <div class="form-group">
        <input type="text" class="form-control" placeholder="Special Rate" id="special_rate"/>
        <button class="btn btn-info update_all">UPDATE</button>
      </div>
    </div>
  </div>
 </div>

 <br>
</section>

<?php  include_once('footer.php'); ?>
<script>

function initDate_picker(){   
    $('.datepicker').datepicker({ 
      format: 'dd-mm-yyyy', 
      autoHide:true,
      pick: function(dateText, inst) {         
              var sdate = moment(dateText.date).format('YYYY-MM-DD');
              $(this).parent().find('input').val(sdate);
              var e = $.Event('keypress');
              e.which = 13; // Enter key           
              $(this).parent().find('.update').trigger(e);
          }
    });
  }

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
         { data: 'all_select' },         
      ],
      "drawCallback": function (settings){
        initDate_picker();
      }
   });

   initDate_picker();
}


ProductDataTable();

$('#search').on('click',function(e){
  e.preventDefault();
  filterTable();
});

function filterTable(){
  let params = {
    'product_name': $('#product_name').val(),
    'bar_code': $('#bar_code').val(),
    'product_code': $('#product_code').val(),
    'brand': $('#brand').val() ,
    'category': $('#category').val(),
    'color': $('#color').val(),
    'size': $('#size').val(),
    'branch': $('#branch').val(),
  }

  ProductDataTable(params);
}

$(document).on('keypress', '.update', function(e){  
  var key = e.which;
  if(key == 13){ // the enter key code
      var id = $(this).data("id");
      var column_name = $(this).data("column");
      var value = $(this).text();
      if($(this).hasClass('datepicker')){
        value = $(this).parent().find('input').val();
      }else if($(this).hasClass('number')){
        value = parseFloat(value);
      }     
      update_product(id, column_name, value);
    }
  
  });

  function update_product(id, column_name, value){
    var data = {
      'id' : id,
      'col' : column_name,
      'val' : value
    }

    $.ajax({
      url:'product_detail.php?update_product=1',
      dataType:'json',
      method:'post',
      data: data
    }).done(function(res){
      toastr.success('Updated Successfully', '', {timeOut: 2000, "positionClass": "toast-bottom-right",});
      filterTable();
    });      
  }

  $('.update_all').on('click', function(){
    var checkedIds = [];
    var _parent = $(this).parent();
    var val = _parent.find('input').val();
    val = parseFloat(val);
    var col = _parent.find('input').attr('id');

      $('#product_list').find('tbody > tr').each(function(){     
        var id = parseInt($(this).find('input[type="checkbox"]:checked').val());
      if(id){
        checkedIds.push(id);
      }
      });
     
      if(!val || checkedIds.length == 0){
        alert('Check the value or select some checkboxes');
        return false;
      }

      let data = {
        'col' : col,
        'val' : val,
        'ids' : checkedIds
      }

      $.ajax({
      url:'product_detail.php?bulk_update_product=1',
      dataType:'json',
      method:'post',
      data: data
    }).done(function(res){
      toastr.success('Updated Successfully', '', {timeOut: 2000, "positionClass": "toast-bottom-right",});
      _parent .find('input').val('');
      filterTable();
    }); 

      
  });
  

</script>
