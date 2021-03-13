<?php
require_once 'Crud.php';

class GetTableData{

    private $crud = null;
    private $table = '';

    public function __construct($table){
        if(strlen($table) == 0){
            die('Required Table Name');
        }else{
            $this->table = $table;
        }

        $this->crud = new Crud();        
    }

    public function fetchTableData(){
        $sql = ''; $data = [];

        switch($this->table){

            case 'product_brand':   
                                $sql = "select Brand_ID,Brand_Code,Brand_Name from product_brand order by Brand_Name"; 
                                break;
            case 'product_category' : 
                                $sql = "select Category_ID,Category_Code,Category_Name from product_category order by Category_Name"; 
                                break;
            case 'branch' : 
                                $sql = "select Branch_ID,Branch_Code,Branch_Name from branch order by Branch_Name"; 
                                break;
            case 'color' : 
                                $sql = "select Color_ID,Color_Code,Color_Name from color order by Color_Name"; 
                                break;
            case 'size' : 
                                $sql = "select Size_ID,Size_Code,Size_Name from size order by Size_Name"; 
                                break;
            default: $sql = '';
        }

        if(strlen($sql) > 0){
            $data = $this->crud->getData($sql);
        }

        return $data;
    }
}