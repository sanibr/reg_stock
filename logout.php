<?php
session_start();
include_once("class/Crud.php");
$crud = new Crud();
$crud->user_logout();
header('location:index.php');
?>