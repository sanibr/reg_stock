<?php
class ChangePassword 
{	
	private $_host = 'localhost';
	private $_username = 'root';
	private $_password = '';
	private $_database = 'infonetu_registration';
	
	protected $connection;
	
	public function __construct()
	{
		
		if (!isset($this->connection)) {
			
			$this->connection = new mysqli($this->_host, $this->_username, $this->_password, $this->_database);
			
			if (!$this->connection) {
				die('Cannot connect to database server');
				exit;
			}			
		}	
		
		return $this->connection;
    }


    public function change_Password($post){

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $current_password = $this->escape_string(trim($post['current_password']));
        $new_password = $this->escape_string(trim($post['new_password']));
        $confirm_password = $this->escape_string(trim($post['confirm_password']));
        $pass = hash('sha256', $current_password);
		
		$query = $this->connection->query("select * from users WHERE user_id =".$_SESSION['user_id']." and password='".$pass."' limit 1");
        $result = $query->fetch_assoc();
       
		if (!empty($result) && sizeof($result)>0 ) {

            if($new_password == $confirm_password ){
                $newpwd = hash('sha256', $new_password); 
                $this->connection->query("update users set password='".$newpwd."' where user_id ='".$_SESSION['user_id']."'");
                $msg = 'success';
            } else{
                $msg = 'The password and confirmation password do not match.';
            }
			
				
		}else{
            $msg = 'The old password you have entered is incorrect';
        }
        
        return  $msg;
		
    }
    

    public function escape_string($value)
	{
		return $this->connection->real_escape_string($value);
	}
    

}
?>
