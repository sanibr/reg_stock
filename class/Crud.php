<?php
include_once 'DbConfig.php';
include_once 'common_settings.php';

class Crud extends DbConfig
{
	private $is_mssql = false;
	public function __construct()
	{
		parent::__construct();
		if(isset($_SESSION['db_type']) && $_SESSION['db_type'] == "mssql"){
			$this->is_mssql == true;
		}
				
	}
	
	public function getData($query, $record = 0, $offset = 0)
	{
	  $rows = array();

	  $limit = $this->sqlLimit($record, $offset);
	  $query .= $limit;	 
	  
	  if($_SESSION['db_type'] == "mysql" || !isset($_SESSION['db_type'])){
	      
		$result = $this->connection->query($query);
		
		 if ($result == false) {
			return false;
		 }	
		
		while ($row = $result->fetch_assoc()) {			
			$rows[] = $row;
		}
		
	  } else if ($_SESSION['db_type'] == "mssql"){
	      
	      	$result = mssql_query($query, $this->connection);
	      	
		    if ($result == false) {
			  return false;
		    } 
		    
		    while ($row = mssql_fetch_array($result)) {
			    $rows[] = $row;
		    }
	  }
		
	
		
		return $rows;
	}
		
	public function execute($query) 
	{
	    if($_SESSION['db_type'] == "mysql" || !isset($_SESSION['db_type'])){
	         
    		$result = $this->connection->query($query);
    		
    		if ($result == false) {
    			//error_log('Error: ' . $query . '<br>' . $this->connection->error);
    			return false;
    		} else {
    			return $this->connection->insert_id;
    		}
	    } else if ($_SESSION['db_type'] == "mssql"){
	        
	        $result = mssql_query($query, $this->connection);
    		
    		if ($result == false) {
    			//error_log('Error: ' . $query . '<br>' . $this->connection->error);
    			return false;
    		} else {
    			return $this->connection->insert_id;
    		}
	        
	    }
	}

	public function sqlLimit($record = 0, $offset = 0){

		if($record == 0 && $offset == 0){
			return '';
		}
		else if($record > 0 && $offset == 0){
			$limit  = " limit $record";
			if ($_SESSION['db_type'] == "mssql"){
				$limit = " OFFSET 0 ROWS FETCH NEXT $record ROWS ONLY";
			}

		}else if($record > 0 && $offset > 0){
			$limit  = " limit $offset, $record";
			if ($_SESSION['db_type'] == "mssql"){
				$limit = " OFFSET $offset ROWS FETCH NEXT $record ROWS ONLY";
			}

		}
		return $limit;
	}
	
	public function number_of_records($query)
	{	
	    if($_SESSION['db_type'] == "mysql" || !isset($_SESSION['db_type'])){
	        
    		$result = $this->connection->query($query);    		
    		return $result->num_rows;
    		
	   } else if ($_SESSION['db_type'] == "mssql"){	       
	        $result = mssql_query($query, $this->connection);	        
	        return mssql_num_rows($result);
	   }
	}
	
	
	
	public function escape_string($value){

		if(isset($_SESSION['db_type']) && $_SESSION['db_type'] == "mssql"){
			return $this->ms_escape_string($value);
		}
		else{
			return $this->connection->real_escape_string($value);			
		return $this->connection->real_escape_string($value);
			return $this->connection->real_escape_string($value);			
		}
	}
	
	public function getSettings(){
		
		$settings = $this->getData("select * from common_settings LIMIT 1");
		
		return (array) $settings[0];
	}
	
	public function login($user, $pass){
		
		$result = $this->getData("select * from users WHERE email = '$user' and password='$pass' LIMIT 1");
		
	
		
		if (!empty($result) && sizeof($result[0])>0 ) {
			session_start();
			$_SESSION['user_id'] = $result[0]['user_id'];
			$_SESSION['user_name'] = $result[0]['username'];
			$_SESSION['user_email'] = $result[0]['email'];
			$_SESSION['dbname'] = $result[0]['dbname'];
			$_SESSION['db_uname'] = $result[0]['db_uname'];
			$_SESSION['db_pwd'] = $result[0]['db_pwd'];	
			$_SESSION['db_type'] = $result[0]['db_type'];
			$_SESSION['db_host'] = $result[0]['db_host'];
			$_SESSION['login'] = true;			
			return true;	
		}else{
			return false;
		}	
		
	}
	
	public function dashboard_data()
	{
		//$where = $_SESSION['is_global']?'':" and p.country_id=".$_SESSION['country_id']."";
		
		if($_SESSION['db_type'] == "mysql" || !isset($_SESSION['db_type'])){
		
		    $invdate = "date(sm.Invoice_Date)";
		
        } else if ($_SESSION['db_type'] == "mssql"){
        
            $invdate = "sm.Invoice_Date";
          
        }
		
		$total_sales = $this->getData("select sum(sm.Grand_Total) as Grand_Total from sales_master sm where is_saved='true'");
		
		$today = $this->getData("select sum(sm.Grand_Total) as Grand_Total from sales_master sm where $invdate ='".date('Y-m-d')."' and is_saved='true'");
		
		list($w_start,$w_end) = $this->x_week_range(date('Y-m-d'));
		
		$week = $this->getData("select sum(sm.Grand_Total) as Grand_Total from sales_master sm  where $invdate between '".$w_start."' and '".$w_end."' and is_saved='true' ");
		
		
		$month = $this->getData("select sum(sm.Grand_Total) as Grand_Total from sales_master sm where $invdate between '".date('Y-m-1')."' and '".date('Y-m-t')."' and is_saved='true' ");
		
	
		
		for ($i = 1; $i < 12; $i++) {
			if ($i==11) {
				$month_start = date('Y-m-1', strtotime("-$i month")); //last 6 month
			}
		}
		$month_end = date('Y-m-t');
		

		
		$chart_data = $this->getData("SELECT month(sm.Invoice_Date) month,sum(sm.Grand_Total) total,sm.Company_Id,c.Company_Name,sm.Branch_Id,b.Branch_Name
        FROM  sales_master sm
		left join company c on sm.Company_Id=c.Company_ID
		left join branch b on sm.Branch_Id=b.Branch_ID
	    where  sm.Invoice_Date between '".$month_start."' and '".$month_end."' and is_saved='true' $where
		group by month(sm.Invoice_Date),sm.Company_Id,c.Company_Name,sm.Branch_Id,b.Branch_Name");
										
										$chart_data_new=[];
										$_mon =[1,2,3,4,5,6,7,8,9,10,11,12];
										
										foreach ($chart_data as $k=>$chart) {
											if (!empty($chart['month'])) {

												$chart_data_new[$chart['Branch_Name']][$chart['month']] = $chart['total'];
												$chart_data_new[$chart['Branch_Name']]['color'] = $chart['Color'];								
											}
										}

										$chart_date = $chart_data_new;

										foreach($chart_data_new as $k => $v){
											$months = array_keys($v);
											$diff = array_diff($_mon,$months);
											if(!empty($diff)){
												foreach($diff as $mon){
													$chart_date[$k][$mon] = 0;
												}
											}
											ksort($chart_date[$k]);

										}
										
		
		$data = ['sales' => ['total' => $this->num_format($total_sales[0]['Grand_Total']), 'today' => $this->num_format($today[0]['Grand_Total']), 'week' => $this->num_format($week[0]['Grand_Total']), 'month' => $this->num_format($month[0]['Grand_Total'])  ],'chart' =>$chart_date ];
				 
		 return $data;			
	}
	
	public function x_week_range($date)
	{
		$ts = strtotime($date);
		$start = (date('w', $ts) == 0) ? $ts : strtotime('last sunday', $ts);
		return array(date('Y-m-d', $start),
		date('Y-m-d', strtotime('next saturday', $start)));
	}

	
	public function is_loggedin(){
		
		return $_SESSION['login'];
	}
	
	public function get_session()
	{		
		return $_SESSION['login'];		
	}
	
	public function user_logout()
	{		
		$_SESSION['login'] = FALSE;		
		session_destroy();		
	}
	
	function is_decimal($val)
	{
		return is_numeric($val) && floor($val) != $val;
	}
	public function format_date($date, $istime = 0)
	{
		if(!$istime){
			return (strtotime($date) > 0) ? date('Y-m-d', strtotime($date)) : '';
		}
		
	}

	public function disp_date($date, $istime = 0)
	{
		if(!$istime){
			return (strtotime($date) > 0) ? date('d-m-Y', strtotime($date)) : '';
		}
		
	}

	public function num_format($val)
	{
			return number_format($val, 2, '.', '');
	}
	
	public function err_log($data, $flag = 0){
		if($flag == 0){
			error_log($data);
		}
		else{
			error_log(print_r($data,1));
		}
		
	}

	private function ms_escape_string($data) {
        if ( !isset($data) or empty($data) ) return '';
        if ( is_numeric($data) ) return $data;

        $non_displayables = array(
            '/%0[0-8bcef]/',            // url encoded 00-08, 11, 12, 14, 15
            '/%1[0-9a-f]/',             // url encoded 16-31
            '/[\x00-\x08]/',            // 00-08
            '/\x0b/',                   // 11
            '/\x0c/',                   // 12
            '/[\x0e-\x1f]/'             // 14-31
        );
        foreach ( $non_displayables as $regex )
            $data = preg_replace( $regex, '', $data );
        $data = str_replace("'", "''", $data );
        return $data;
    }
	
	
}
?>
