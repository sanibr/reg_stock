<?php
include_once("check_login.php");
include_once('class/common_settings.php');
include_once("class/Crud.php");
$crud = new Crud();
$data = $crud->dashboard_data();
include_once('menu.php');
?>

<div class="m-b-md">
	<h3 class="m-b-none">Dashboard</h3>
</div>
<section class="panel panel-default">
                <div class="row m-l-none m-r-none bg-light lter">
                  <div class="col-sm-6 col-md-3 padder-v b-r b-light">
                    <span class="fa-stack fa-2x pull-left m-r-sm">
                      <i class="fa fa-circle fa-stack-2x text-info"></i>
                      <i class="fa fa-check fa-stack-1x text-white"></i>
                    </span>
                    <a class="clear" href="#">
                      <span class="h3 block m-t-xs"><strong><?=$data['sales']['total'] ?></strong></span>
                      <small class="text-muted text-uc">Total Sales</small>
                    </a>
                  </div>
                  <div class="col-sm-6 col-md-3 padder-v b-r b-light lt">
                    <span class="fa-stack fa-2x pull-left m-r-sm">
                      <i class="fa fa-circle fa-stack-2x text-warning"></i>
                      <i class="fa fa-dashboard fa-stack-1x text-white"></i>
                      <span class="easypiechart pos-abt" data-percent="100" data-line-width="4" data-track-Color="#fff" data-scale-Color="false" data-size="50" data-line-cap='butt' data-animate="2000" data-target="#bugs" data-update="3000"></span>
                    </span>
                    <a class="clear" href="#">
                      <span class="h3 block m-t-xs"><strong id="bugs"><?=$data['sales']['today'] ?></strong></span>
                      <small class="text-muted text-uc">Today</small>
                    </a>
                  </div>
                  <div class="col-sm-6 col-md-3 padder-v b-r b-light">                     
                    <span class="fa-stack fa-2x pull-left m-r-sm">
                      <i class="fa fa-circle fa-stack-2x text-danger"></i>
                      <i class="fa fa-align-justify fa-stack-1x text-white"></i>
                      <span class="easypiechart pos-abt" data-percent="100" data-line-width="4" data-track-Color="#f5f5f5" data-scale-Color="false" data-size="50" data-line-cap='butt' data-animate="3000" data-target="#firers" data-update="5000"></span>
                    </span>
                    <a class="clear" href="#">
                      <span class="h3 block m-t-xs"><strong id="firers"><?=$data['sales']['week'] ?></strong></span>
                      <small class="text-muted text-uc">This Week</small>
                    </a>
                  </div>
                  <div class="col-sm-6 col-md-3 padder-v b-r b-light lt">
                    <span class="fa-stack fa-2x pull-left m-r-sm">
                      <i class="fa fa-circle fa-stack-2x text-success"></i>
                      <i class="fa fa-calendar fa-stack-1x text-white"></i>
                    </span>
                    <a class="clear" href="#">
                      <span class="h3 block m-t-xs"><strong><?=$data['sales']['month'] ?></strong></span>
                      <small class="text-muted text-uc">This Month</small>
                    </a>
                  </div>
                </div>
              </section>
<div class="row">
	<div class="col-md-12">
		<section class="panel panel-default">
			<header class="panel-heading font-bold">Sales Details</header>
			<div class="panel-body">
				<div id="flot-bar"  style="height:240px"></div>
			</div>
			<footer class="panel-footer bg-white no-padder">
				
			</footer>
		</section>
	</div>	
</div>
 <?php  include_once('footer.php'); ?>
 <script>
 
	var ticks = [
	    [1, "JAN"], [2, "FEB"], [3, "MAR"], [4, "APR"], [5, "MAY"], [6, "JUN"],[7, "JUL"],[8, "AUG"],[9, "SEP"],[10, "OCT"],[11, "NOV"],[12, "DEC"]
	];
	
	
	$("#flot-bar").length && $.plot($("#flot-bar"), [
		<?php
			foreach($data['chart'] as $k => $v){
				$data1='';
				foreach($v as $m=>$tot){
					if ($m!='color') {
						$data1 .='['.$m.','.$tot.'],';
						/*for ($i=1;$i<=12;$i++) {
							if ($i==$m) {
								$data1 .='['.$m.','.$tot.'],';
							}else{
								$data1 .='['.$i.',0],';
							}
						}*/
					}
						
				}
				echo '{data: ['.$data1.'], label: "'.$k.'"},';
			}
		?>	
	],
	{
		series: {
			lines: {
				show: true,
				lineWidth: 1,
				fill: true,
				fillColor: {
					colors: [{
							opacity: 0.2
						}, {
							opacity: 0.1
						}]
				}
			},
			points: {
				show: true
			},
			shadowSize: 2
		},
		grid: {
			hoverable: true,
			clickable: true,
			tickColor: "#f0f0f0",
			borderWidth: 0
		},
		colors: ["#3abf9a","#89cb4e",'#f73b3b','#c836ec'],
		xaxis: {
			ticks: ticks
		},
		yaxis: {
			ticks: 10,
			tickDecimals: 0
		},
		tooltip: true,
		tooltipOpts: {
			content: "%s - %y.4",
			defaultTheme: false,
			shifts: {
				x: 0,
				y: 20
			}
		}
	}
	);

 </script>
 