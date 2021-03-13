<?php
require_once 'GetTableData.php';

class ProductDetails{
    private $crud = null;
    private $post = null;
    private $get = null;

    public function __construct($post = null, $get = null){
        $this->crud = new Crud();
        $this->post = $post;
        $this->get = $get;        
    }

    public function getTableData($table){
        $fetch = new GetTableData($table);
        return $fetch->fetchTableData();
    }    

    public function getProductList(){

        $where = $limit = "";

        if(isset($this->post['product_name']) && strlen(trim($this->post['product_name'])) > 0){
            $where .= " and pd.Product_Detail_Name like '{$this->crud->escape_string(trim($this->post['product_name']))}%'";
        }

        if(isset($this->post['bar_code']) && strlen(trim($this->post['bar_code'])) > 0){
            $where .= " and pd.Barcode like '{$this->crud->escape_string(trim($this->post['bar_code']))}%'";
        }

        if(isset($this->post['product_code']) && strlen(trim($this->post['product_code'])) > 0){
            $where .= " and pd.Product_Code like '{$this->crud->escape_string(trim($this->post['product_code']))}%'";
        }

        if(isset($this->post['brand']) && (int)$this->post['brand'] > 0){
            $where .= " and pm.Brand_ID = '".(int)$this->post['brand']."'";
        }

        if(isset($this->post['category']) && (int)$this->post['category'] > 0){
            $where .= " and pm.Category_ID = '".(int)$this->post['category']."'";
        }

        if(isset($this->post['color']) && (int)$this->post['color'] > 0){
            $where .= " and pd.Color_Id = '".(int)$this->post['color']."'";
        }

        if(isset($this->post['size']) && (int)$this->post['size'] > 0){
            $where .= " and pd.Size_Id = '".(int)$this->post['size']."'";
        }

        if(isset($this->post['branch']) && (int)$this->post['branch'] > 0){
            $where .= " and pd.Branch_ID = '".(int)$this->post['branch']."'";
        }       

        $query = "select pd.*, b.branch_name from product_detail pd
                  left join product_master pm on pd.Product_Id=pm.Product_Id
                  left join branch b on pd.branch_id=b.branch_id where 1=1 ";


        if($this->post["length"] != -1){
            $limit = ' LIMIT ' . $this->post['start'] . ', ' . $this->post['length'];
        }

        $order = " order by pd.Product_Detail_Id";

        $number_filter_row = $this->crud->number_of_records($query.$where);
        $result = $this->crud->getData($query.$where.$order.$limit);    
        $data = [];

        foreach($result as $row){
            $p_id = $row['Product_Detail_Id'];

            $disc_rate = $this->crud->num_format($row['Retail_Rate']-$row['Product_Detail_Discount']);

            $stock = $this->editColumn($p_id, $row, 'Stock', 'number');
            $p_rate = $this->editColumn($p_id, $row, 'Purchase_Rate', 'number');
            $p_cost = $this->editColumn($p_id, $row, 'Purchase_Cost', 'number');
            $w_rate = $this->editColumn($p_id, $row, 'Wholesale_Rate', 'number');
            $r_rate = $this->editColumn($p_id, $row, 'Retail_Rate', 'number');
            $mrp = $this->editColumn($p_id, $row, 'MRP', 'number');
            $from = $this->editColumn($p_id, $row, 'Sp_RateFrom', 'date');
            $sp_rate = $this->editColumn($p_id, $row, 'Special_Rate', 'number');
            $to = $this->editColumn($p_id, $row, 'Sp_RateTo', 'date');
            $disc = $this->editColumn($p_id, $row, 'Product_Detail_Discount', 'number');
            $disc_amt = $this->editColumn($p_id, $row, 'Product_Detail_Disc_Amount', 'number');

            $data[] = [ 'product_id' => $row['Product_Detail_Id'],
                        'code' => $row['Product_Code'],
                        'barcode' => $row['Barcode'],
                        'product_name' => $row['Product_Detail_Name'],

                        'stock' =>  $stock,
                        'p_rate' => $p_rate,
                        'p_cost' => $p_cost,
                        'w_rate' => $w_rate,
                        'r_rate' => $r_rate,
                        'mrp' =>  $mrp,
                        'from' => $from,
                        'sp_rate' => $sp_rate,
                        'to' => $to,
                        'disc' => $disc,
                        'disc_amt' =>  $disc_amt,
                        'disc_rate' => $this->crud->num_format($disc_rate) ,
                        'branch' => $row['branch_name'],
                        'all_select' => '<input type="checkbox" value="'.$row['Product_Detail_Id'].'" class="form-control" />'                                         
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

    private function editColumn($id, $data, $column, $format = ''){
        $class = $input ='';
        switch($format){
            case 'number' : $data[$column] = $this->crud->num_format($data[$column]);
                            $class = "number"; 
                            break;
            case 'date' : 
                            $data[$column] = $this->crud->disp_date($data[$column]);
                            $input = '<input type="hidden" class="date"/>';
                            $class = "datepicker";
                            break;
            default : $data[$column] = $data[$column];
        }

        return '<div contenteditable class="update '.$class.'" data-id="'.$id.'" data-column="'.$column.'">' . $data[$column] . '</div>'.$input.'';
    }

    public function updateProduct(){
        $col = $this->crud->escape_string($this->post['col']);
        $value = $this->crud->escape_string($this->post['val']);
        $id = (int)$this->post['id'];
        $update = "update product_detail set `$col` =  '{$value}' where Product_Detail_Id = {$id}";        
        $this->crud->execute($update);
        return json_encode(['save' => true]);
        
    }

    public function bulkUpdateProduct(){

        switch($this->post['col']){
            case 'disc_per': $col = 'Product_Detail_Discount';
                             break;
            case 'disc_amt': $col = 'Product_Detail_Disc_Amount';
                             break;
            case 'special_rate': $col = 'Special_Rate';
                            break;
        }
        $value = (float) $this->post['val'];

        if(sizeof($this->post['ids']) > 0){
            $ids =  implode(',', $this->post['ids']);
            $update = "update product_detail set {$col} = $value where Product_Detail_Id in ($ids)";
            $this->crud->execute($update);
            return json_encode(['save' => true]);
        }
        
    }
}