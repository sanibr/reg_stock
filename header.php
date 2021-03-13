<?php
include_once("check_login.php");
?>
<!DOCTYPE html>
<html lang="en" class="app">
<head>
  <meta charset="utf-8" />
  <title>INFONETUAE</title>
  <meta name="description" content="app, web app, responsive, admin dashboard, admin, flat, flat ui, ui kit, off screen nav" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" /> 
  <link rel="stylesheet" href="css/bootstrap.css" type="text/css" />
  <link rel="stylesheet" href="css/animate.css" type="text/css" />
  <link rel="stylesheet" href="css/font-awesome.min.css" type="text/css" />
  <link rel="stylesheet" href="css/font.css" type="text/css" />
<link rel="stylesheet" href="js/fuelux/fuelux.css" type="text/css" />
<link rel="stylesheet" href="js/datepicker/datepicker.css" type="text/css" />
<link rel="stylesheet" href="js/slider/slider.css" type="text/css" />
<!-- <link rel="stylesheet" href="js/datatables/datatables.css" type="text/css"/> -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" />
  <link rel="stylesheet" href="css/app.css" type="text/css" />
 
  <script src="js/ckeditor/ckeditor.js"></script>

  

  
<!-- new datepicker https://github.com/fengyuanchen/datepicker -->
<link rel="stylesheet" href="calendar/datepicker.min.css" />

<!--  https://github.com/CodeSeven/toastr -->
<link rel="stylesheet" href="js/toastr/toastr.min.css"/>
  
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />  
  
  <!--[if lt IE 9]>
    <script src="js/ie/html5shiv.js"></script>
    <script src="js/ie/respond.min.js"></script>
    <script src="js/ie/excanvas.js"></script>
  <![endif]-->
</head>
<section class="vbox">
<header class="bg-dark dk header navbar navbar-fixed-top-xs">
      <div class="navbar-header aside-md">
        <a class="btn btn-link visible-xs" data-toggle="class:nav-off-screen,open" data-target="#nav,html">
          <i class="fa fa-bars"></i>
        </a>
        <a href="#" class="navbar-brand" data-toggle="fullscreen"><img src="images/InfonetPNG.png" class="m-r-sm" style="max-height:40px !important;"></a>
        <a class="btn btn-link visible-xs" data-toggle="dropdown" data-target=".nav-user">
          <i class="fa fa-cog"></i>
        </a>
      </div>          
      <ul class="nav navbar-nav navbar-right m-n hidden-xs nav-user">        
        <li class="dropdown" id="open_li">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
			<?=$_SESSION['user_name']?><b class="caret"></b>
          </a>
          <ul class="dropdown-menu animated fadeInRight">
            <span class="arrow top"></span>
            <li>
              <a href="logout.php">Logout</a>
            </li>
            <li>
              <a href="change-password.php">Change Password</a>
            </li>
          </ul>
        </li>
      </ul>      
    </header>