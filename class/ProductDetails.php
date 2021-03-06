<?php

class ProductDetails{
    private $crud = null;
    private $post = null;
    private $get = null;

    public function __construct($post = null, $get = null){
        $this->crud = new Crud();
        $this->post = $post;
        $this->get = $get;        
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
            $where .= " and pd.Company_Id = '".(int)$this->post['brand']."'";
        }


        if(isset($this->post['category']) && (int)$this->post['category'] > 0){
            $where .= " and pd.Category = '".(int)$this->post['category']."'";
        }

        $query = "select pd.*, b.branch_name from product_detail pd left join branch b on pd.branch_id=b.branch_id where 1=1 ";


        if($this->post["length"] != -1){
            $limit = ' LIMIT ' . $this->post['start'] . ', ' . $this->post['length'];
        }

        $order = " order by pd.Product_Detail_Id";

        $number_filter_row = $this->crud->number_of_records($query.$where);
        $result = $this->crud->getData($query.$where.$order.$limit);    
        $data = [];

        $this->crud->err_log($result,1);

        foreach($result as $row){
            $data[] = [ 'product_id' => $row['Product_Detail_Id'],
                        'code' => $row['Product_Code'],
                        'barcode' => $row['Barcode'],
                        'product_name' => $row['Product_Detail_Name'],
                        'stock' => $this->crud->num_format($row['Stock']),
                        'p_rate' => $this->crud->num_format($row['Purchase_Rate']),
                        'p_cost' => $this->crud->num_format($row['Purchase_Cost']),
                        'w_rate' => $this->crud->num_format($row['Wholesale_Rate']),
                        'r_rate' => $this->crud->num_format($row['Retail_Rate']),
                        'mrp' =>  $this->crud->num_format($row['MRP']),
                        'from' => $this->crud->disp_date($row['Manufactoring_Date']),
                        'sp_rate' => 'dummy',
                        'to' => $this->crud->disp_date($row['Expiry_Date']),
                        'disc' => 'dummy',
                        'disc_amt' => 'dummy',
                        'disc_rate' => 'dummy',
                        'branch' => $row['branch_name'],            
                        //'voucher_date' => $this->crud->disp_date($row['']),                      
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