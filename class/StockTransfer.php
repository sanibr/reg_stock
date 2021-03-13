<?php

class StockTransfer{

    private $crud = null;
    private $stockMasterId = 0;
    private $stockDetailId = 0;
    private $post = null;
    private $get = null;
    private $voucherNo = 0;

    public function __construct($post = null, $get = null){
        $this->crud = new Crud();
        if(isset($post) && !empty($post)){
            $this->post = $post;            
        }
        if(isset($get) && !empty($get)){
            $this->get = $get;         
        }
    }

    public function saveStock($isExcel = false){       
        if($this->getStockMasterId() == 0){
            $ctime = date('H:i:s');      
            if(strtotime($this->post['transaction_date']) == 0){
                $this->post['transaction_date'] = date('d-m-Y');
            }
            $voucherNo = $this->crud->getData("SELECT IFNULL(MAX(StockTransfer_No)+1, 1) voucher FROM stocktransfer_master");
            $this->voucherNo = $voucherNo[0]['voucher'];     
            $masterQuery = "insert into stocktransfer_master(StockTransfer_No,Delivery_No,StockTransfer_Date,StockTransfer_Time,Branch_From,Branch_To,Is_Saved)
                            values({$this->voucherNo},'{$this->crud->escape_string($this->post['delivery_no'])}', '{$this->crud->format_date($this->post['transaction_date'])}', '{$ctime}',
                                    ".(int)$this->post['from_branch'].", ".(int)$this->post['to_branch'].", 0)";           
            $this->stockMasterId = $this->crud->execute($masterQuery);
        }        
        $this->saveStockDetail();
        if(!$isExcel){
            $data = ['stock_master_id' => $this->getStockMasterId() , 'voucher_no' => $this->getVoucherNo() ];
            return json_encode($data);
        }
    }

    private function saveStockDetail(){
        $order_qty = 0;
        $gross_amount = $this->post['quantity'] * $this->post['sales_rate'];
        $discount_amt = 0;
        $net_amount =  $gross_amount - $discount_amt;
        $tax_amount = ($net_amount * $this->post['tax'])/100;
        $amount = $net_amount + $tax_amount;

        if($this->getStockDetailId() > 0){
            $update = "update stocktransfer_detail set 
                                            Product_Id = ".(int)$this->post['product_id']." ,
                                            Product_Detail_Id =".(int)$this->post['product_detail_id']. " ,
                                            Barcode = ".(int)$this->post['barcode']." ,
                                            Quantity = ".(float)$this->post['quantity']." ,
                                            Base_Unit_Id = ".(int)$this->post['unit']." ,
                                            Tax_Percentage = ".(float)$this->post['tax'].",
                                            Sales_Rate = ".(float)$this->post['sales_rate'].",
                                            Gross_Amount = {$gross_amount},
                                            Discount = {$discount_amt},
                                            Net_Amount = {$net_amount} ,
                                            Tax_Amount = {$tax_amount} ,
                                            Amount =  {$amount}             
                                            where StockTransfer_Detail_Id = {$this->getStockDetailId()}";          
            $this->crud->execute($update);
        }else{
            $DeytailQuery = "insert into stocktransfer_detail(StockTransfer_Master_Id, Product_Id, Product_Detail_Id, Barcode, Order_Qty, Quantity, Base_Unit_Id, 
            Tax_Percentage, Sales_Rate, Gross_Amount, Discount, Net_Amount, Tax_Amount, Amount)
                                values({$this->getStockMasterId()}, ".(int)$this->post['product_id'].", ".(int)$this->post['product_detail_id'].",
                                ".(int)$this->post['barcode'].",{$order_qty}, ".(float)$this->post['quantity'].", ".(int)$this->post['unit'].", 
                                ".(float)$this->post['tax'].", ".(float)$this->post['sales_rate'].", {$gross_amount}, {$discount_amt},
                                {$net_amount}, {$tax_amount}, {$amount})";                                
            $this->stockDetailId = $this->crud->execute($DeytailQuery);
        }

        if($this->getStockMasterId() > 0){
            $this->updateTotal();
        }
    }

    public function setStockMasterId($id){
        $this->stockMasterId = (int) $id;
    }


    public function getStockMasterId(){
        return (int) $this->stockMasterId;
    }

    public function setStockDetailId($id){
         $this->stockDetailId = (int)$id;
    }

    public function getStockDetailId(){
        return (int) $this->stockDetailId;
    }

    public function updateTotal(){

        $total = $this->crud->getData("select sum(Gross_Amount) Gross_Amount, sum(Discount) Discount, sum(Net_Amount) Net_Amount, sum(Tax_Amount) Tax_Amount, sum(Amount) Amount 
                                        from stocktransfer_detail where StockTransfer_Master_Id=".$this->getStockMasterId()." group by StockTransfer_Master_Id");
        $total = $total[0];
        $grand_total = $total['Amount'] - $total['Discount'];

        $update = "update stocktransfer_master set 
                            Total_Gross_Amount = {$total['Gross_Amount']} ,
                            Total_Discount_Amount = {$total['Discount']} ,
                            Total_Net_Amount = {$total['Net_Amount']} ,
                            Total_Tax_Amount = {$total['Tax_Amount']} ,
                            Total_Amount = {$total['Amount']} ,
                            Grand_Total = {$grand_total}
                   where StockTransfer_Master_Id = ".$this->getStockMasterId()." ";                   
        $this->crud->execute($update);
    }

    public function setVoucherNo($voucherno){
        $this->voucherNo = (int) $voucherno;
    }

    public function getVoucherNo(){
        return (int) $this->voucherNo;
    }

    public function getDatatable(){

        $where = $limit = "";

        if(isset($this->post['voucher_no']) /*&& (int)$this->post['voucher_no'] > 0*/){
            $where .= " and sm.StockTransfer_No = ".(int)$this->post['voucher_no']."";
        }
        
        $query =  " SELECT sd.StockTransfer_Detail_Id,sd.StockTransfer_Master_Id,sd.Product_Id,sd.Product_Detail_Id,sd.Barcode,
                        pd.Product_Code,pd.Product_Detail_Name,sd.Order_Qty,sd.Quantity,sd.Base_Unit_Id,u.Unit_Name,sd.Gross_Amount,
                        sd.Tax_Amount,sd.Amount,sm.Total_Gross_Amount,sm.Total_Discount_Amount,sm.Total_Net_Amount,sm.Total_Tax_Amount,sm.Total_Amount,
                        sm.Is_Saved
                    FROM stocktransfer_detail sd
                    JOIN stocktransfer_master sm ON sd.StockTransfer_Master_Id = sm.StockTransfer_Master_Id
                    LEFT JOIN product_detail pd ON sd.Product_Detail_Id=pd.Product_Detail_Id
                    LEFT JOIN unit u ON sd.Base_Unit_Id=u.Unit_Id where 1=1";       

        
        if($this->post["length"] != -1){
            $limit = $this->crud->sqlLimit($this->post['length'], $this->post['start']);
        } 

        $order = " order by sd.StockTransfer_Detail_Id desc";

        $number_filter_row = $this->crud->number_of_records($query.$where);
        $result = $this->crud->getData($query.$where.$order.$limit);

        $data = [];        
        foreach($result as $row){
            $action = '';
            if($row['Is_Saved'] == 0){
                $action = '<i data-id="'.$row['StockTransfer_Detail_Id'].'" class="fa fa-pencil-square-o edit"></i> <i data-id="'.$row['StockTransfer_Detail_Id'].'" class="fa fa-trash-o delete"></i>';
            }         
            $data[] = [ $row['StockTransfer_Detail_Id'],
                        $row['Product_Code'],
                        $row['Barcode'],
                        $row['Product_Detail_Name'],
                        $row['Order_Qty'],
                        $this->crud->num_format($row['Quantity']),
                        $row['Unit_Name'],
                        $this->crud->num_format($row['Gross_Amount']),
                        $this->crud->num_format($row['Tax_Amount']),
                        $this->crud->num_format($row['Amount']),
                        $action,                       
                        'tot_gross_amt' => $this->crud->num_format($row['Total_Gross_Amount']),
                        'tot_discount' => $this->crud->num_format($row['Total_Discount_Amount']),
                        'tot_net_amt' => $this->crud->num_format($row['Total_Net_Amount']),
                        'tot_tax_amt' => $this->crud->num_format($row['Total_Tax_Amount']),
                        'tot_amt' => $this->crud->num_format($row['Total_Amount']),
                    ];
        }         
        
        $output = array(
                        "draw"              =>  intval($this->post["draw"]),
                        "recordsTotal"      =>  $number_filter_row,
                        "recordsFiltered"   =>  $number_filter_row,
                        "data"              =>  $data
                    );
   
        return json_encode($output);
    }

    public function getStockDetails(){
        if($this->getStockDetailId() > 0){
            $data = $this->crud->getData("select s.*,pd.Product_Detail_Name product_name,pd.Stock stock from stocktransfer_detail s
                                            left join product_detail pd on pd.Product_Detail_Id=s.Product_Detail_Id
                                            where s.StockTransfer_Detail_Id = ".$this->getStockDetailId()."");
            return $data[0];
        }
    }

    public function saveStockMaster(){
        if($this->getStockMasterId() > 0){
            $update ="update stocktransfer_master set Is_Saved = 1 where StockTransfer_Master_Id =".$this->getStockMasterId() ."";
            $this->crud->execute($update);
        }
        $v = $this->crud->getData("select StockTransfer_No voucher_no from stocktransfer_master where StockTransfer_Master_Id =".$this->getStockMasterId() ."");
        return json_encode(['voucher_no' => $v[0]['voucher_no']]);
    }

    public function searchVoucher(){

        if((int)$this->get['searchVoucher'] == 1){
            $where = $limit = "";
            if(strtotime($this->post['from_date']) > 0){
                $where .= " and StockTransfer_Date >= '{$this->crud->format_date($this->post['from_date'])}'";
            }

            if(strtotime($this->post['to_date']) > 0){
                $where .= " and StockTransfer_Date <= '{$this->crud->format_date($this->post['to_date'])}'";
            }

            if((int)$this->post['voucher_no'] > 0){
                $where .= " and StockTransfer_No = ".(int)$this->post['voucher_no']."";
            }

            if((int)$this->post['from_branch'] > 0){
                $where .= " and Branch_From = ".(int)$this->post['from_branch']."";
            }

            if((int)$this->post['to_branch'] > 0){
                $where .= " and Branch_To = ".(int)$this->post['to_branch']."";
            }

            $query = "select sm.StockTransfer_Master_Id,sm.StockTransfer_No,sm.Delivery_No,sm.StockTransfer_Date,sm.Branch_From,b.Branch_Name from_branch,
                        sm.Branch_To,b1.Branch_Name to_branch,sm.Is_Saved
                        from stocktransfer_master sm left join branch b on sm.Branch_From = b.Branch_ID
                        left join branch b1 on sm.Branch_To = b1.Branch_ID
                        where 1=1 {$where}"; 

            if($this->post["length"] != -1){
                $limit = $this->crud->sqlLimit($this->post['length'], $this->post['start']);
            }
    
            $order = " order by sm.StockTransfer_No";
    
            $number_filter_row = $this->crud->number_of_records($query.$where);
            $result = $this->crud->getData($query.$where.$order.$limit);    
            $data = [];

            foreach($result as $row){
                $data[] = [ 'voucher_no' => $row['StockTransfer_No'],
                            'voucher_date' => $this->crud->disp_date($row['StockTransfer_Date']),
                            'from_branch' => $row['from_branch'],
                            'to_branch' => $row['to_branch'],
                            'from_branch_id' =>  $row['Branch_From'],
                            'to_branch_id' =>  $row['Branch_To'],
                            'delivery_no' =>  $row['Delivery_No'],
                            'is_saved' =>  $row['Is_Saved'],
                            'master_id' =>  $row['StockTransfer_Master_Id'],
                            ];

            }
            $output = array(
                "draw"              =>  intval($this->post["draw"]),
                "recordsTotal"      =>  $number_filter_row,
                "recordsFiltered"   =>  $number_filter_row,
                "data"              =>  $data
            );

            return json_encode($output);

        }
    }

    public function ProcessExcelData($data = []){
        
        if(sizeof($data) > 0){
            foreach($data as $key => $val){
              $prd_code = trim($val[0]);
              $qty = trim($val[1]);
              if(strlen($prd_code) == 0 &&  (int) $qty == 0){
                continue;
              }      
              $prd_details = $this->getProductDetailByCode($prd_code);             
              if(empty($prd_details)){                
                continue;
              }
              /*if($qty > $prd_details['Stock']){
                  $invaliddata[] = ['product_id' => $prd_details['Product_Id'],'product_detail_id' => $prd_details['Product_Detail_Id'],'product_code' => $prd_code,'available_qty' => $prd_details['Stock'],'barcode' => $prd_details['Barcode']]; 
                  continue;
              }*/

              $this->post['quantity'] = $qty;
              $this->post['sales_rate'] = $prd_details['Retail_Rate'];
              $this->post['tax'] = $prd_details['Tax_Percentage'];
              $this->post['product_id'] = $prd_details['Product_Id'];
              $this->post['product_detail_id'] = $prd_details['Product_Detail_Id'];
              $this->post['barcode'] = $prd_details['Barcode'];
              $this->post['unit'] = $prd_details['Base_Unit_Id'];

              if((int)$this->post['from_branch'] != (int)$this->post['to_branch']){
                $this->saveStock(true);
                $this->setStockDetailId(0); //Reset it else it will update the stocke detail
              }
            }

            return json_encode(['stock_master_id' => $this->getStockMasterId(), 'voucher_no' => $this->getVoucherNo()]);
        }
    }

    private function getProductDetailByCode($code){      
        $query = "select pd.Product_Detail_Id,pd.Product_Id,pd.Barcode,pd.Stock,pd.Retail_Rate,pm.Base_Unit_Id,
                    pm.Tax_Percentage from product_detail pd
                    LEFT JOIN product_master pm on pd.Product_Id=pm.Product_Id where pd.Branch_Id=".$this->post['from_branch']." and  pd.Product_Code = '".$this->crud->escape_string($code)."' order by pd.Product_Detail_Id desc";
         
        $data = $this->crud->getData($query, 1);
        return isset($data[0])? $data[0] : [];
    }

    

}