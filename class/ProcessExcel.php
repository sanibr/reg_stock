<?php

/*require "vendor/autoload.php"; // need php 7.4
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;*/

require './spreadsheet/vendor/autoload.php';  //php 5.6

class ProcessExcel{

    private $allowedextension =[];
    private $file = [];
    private $fileinfo = [];
    private $post = [];
    private $fileFieldName = '';
    private $crud = null;
    private $spreadsheetObj = null;
    
    
    

    public function __construct($post = [], $file = []){
        $this->crud = new Crud();
        $this->post = $post;
        $this->file = $file;        
    }

    private function getAllowedExtension(){
        return (array) $this->allowedextension;
    }

    public function setAllowedExtension($ext){
        $this->allowedextension = (array)$ext;
    }

    private function setFileInfo(){
        $this->fileinfo = pathinfo($this->file[$this->getFileFieldName()]['name']);
    }

    public function getFileInfo($key = ''){
        if(strlen($key) == 0){
            return $this->fileinfo;
        }else{
            return $this->fileinfo[$key];
        }
        
    }

    public function setFileFieldName($name){
        $this->fileFieldName = $name;
        $this->setFileInfo();
    }

    private function getFileFieldName(){
       return (string)$this->fileFieldName;
    }

    public function processData(){

        if(in_array($this->getFileInfo('extension'), $this->getAllowedExtension())){
            
            $this->setSpreadSheetObj($this->file[$this->getFileFieldName()]['tmp_name']);
            $data = $this->getExcelData();
            return ['file_details'=> $this->getFileInfo(), 'data'=> array_slice($data,1)];
        }
        return ['error' => 'Not supportted file'];
        
    }

    private function setSpreadSheetObj($tmp_path){
        //$this->spreadsheetObj = \PhpOffice\PhpSpreadsheet\IOFactory::load($tmp_path);
        $this->spreadsheetObj = \PhpOffice\PhpSpreadsheet\IOFactory::load($tmp_path);
    }

    private function getExcelData(){
        return $this->spreadsheetObj->getActiveSheet()->toArray();
    }

}