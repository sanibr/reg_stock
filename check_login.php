<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include_once("class/Crud.php");
$crud = new Crud();
$res = $crud->is_loggedin();
if (!$crud->is_loggedin()) {
	header('location:index.php');
}
?>