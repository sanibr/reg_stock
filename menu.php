<?php include_once('header.php'); ?>
<section>
      <section class="hbox stretch">
        <!-- .aside -->
        
        <aside class="bg-dark lter aside-md hidden-print" id="nav">
<a href="#" class="navbar-brand" data-toggle="fullscreen"><img src="images/ImarkWhiteText.png" class="m-r-sm" style="max-height:40px !important; width:165px !important"></a> 
          <section class="vbox">
           
            <section class="w-f scrollable">
              <div class="slim-scroll" data-height="auto" data-disable-fade-out="true" data-distance="0" data-size="5px" data-color="#333333">
                
                <!-- nav -->
                <nav class="nav-primary hidden-xs">
                  <ul class="nav">
                 
                  <li <?=basename($_SERVER['PHP_SELF'])=="dashboard.php"?'class="active"':'' ?>  >
                      <a href="dashboard.php"  >
                        <i class="fa fa-dashboard icon">
											<b class="bg-danger"></b>
                        </i>
                        <span>Dashboard</span>
                      </a>
                  </li>
                  <li >
                      <a href="#layout"  >
                        <i class="fa fa-truck icon">
                          <b class="bg-warning"></b>
                        </i>
                        <span class="pull-right">
                          <i class="fa fa-angle-down text"></i>
                          <i class="fa fa-angle-up text-active"></i>
                        </span>
                        <span>Sales</span>
                      </a>
                      <ul class="nav lt">
                        <li <?=basename($_SERVER['PHP_SELF'])=="sales.php"?'class="active"':'' ?> >
                          <a href="sales.php" >                                                        
                            <i class="fa fa-angle-right"></i>
                            <span>Sales Report </span>
                          </a>
                        </li>
                      </ul>
                    </li>

                    <li>
                      <a href="#layout"  >
                        <i class="fa fa-shopping-cart icon">
                          <b class="bg-primary"></b>
                        </i>
                        <span class="pull-right">
                          <i class="fa fa-angle-down text"></i>
                          <i class="fa fa-angle-up text-active"></i>
                        </span>
                        <span>Purchase</span>
                      </a>
                      <ul class="nav lt">
                        <li <?=basename($_SERVER['PHP_SELF'])=="purchase.php"?'class="active"':'' ?> >
                          <a href="purchase.php" >                                                        
                            <i class="fa fa-angle-right"></i>
                            <span>Purchase Report </span>
                          </a>
                        </li>
                      </ul>
                    </li>


                    <li>
                      <a href="#layout"  >
                        <i class="fa fa-exchange icon">
                          <b class="bg-info"></b>
                        </i>
                        <span class="pull-right">
                          <i class="fa fa-angle-down text"></i>
                          <i class="fa fa-angle-up text-active"></i>
                        </span>
                        <span>Stock</span>
                      </a>
                      <ul class="nav lt">
                      <li <?=basename($_SERVER['PHP_SELF'])=="stock_transfer.php"?'class="active"':'' ?> >
                          <a href="stock_transfer.php" >                                                        
                            <i class="fa fa-angle-right"></i>
                            <span>Stock Transfer</span>
                          </a>
                        </li>
                        <li <?=basename($_SERVER['PHP_SELF'])=="stock.php"?'class="active"':'' ?> >
                          <a href="stock.php" >                                                        
                            <i class="fa fa-angle-right"></i>
                            <span>Stock Report </span>
                          </a>
                        </li>
                      </ul>
                    </li>

                   
                    
                  
                  </ul>
                </nav>
                <!-- / nav -->
              </div>
            </section>
            
            
          </section>
        </aside>
 <section id="content">
<section class="vbox">
<section class="scrollable padder">
<ul class="breadcrumb no-border no-radius b-b b-light pull-in">
	<li>
		<a href="dashboard.php">
			<i class="fa fa-home"></i> Home</a>
	</li>
	<?php
		if (isset($bread_cums) && !empty($bread_cums)){
			foreach ($bread_cums as $text=>$url) {
				echo '<li><a href="'.$url.'">'.$text.'</a></li>';
			}
		}
	?>

</ul>