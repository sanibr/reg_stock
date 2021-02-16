<?php
include_once("check_login.php");
include_once("class/Change_password.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$chanpwd = new ChangePassword();
	$result = $chanpwd->change_Password($_POST);
	
	if($result == 'success'){
		session_destroy(); ?>
		<script>
		 alert('Password Changed succesfully please login again');
		 window.location='index.php';
		</script>
	<?
	} else{		
		?>
		<script>
		 alert('<?=$result?>');
		 window.location='change-password.php';
		</script>
		<?
		//header('location:change-password.php');
	}
}
$bread_cums = ['Change Password'=>''];

include_once('menu.php');
?>

              <div class="m-b-md">
                <h3 class="m-b-none">Change Password</h3>
              </div>
              <section class="panel panel-default">
                <header class="panel-heading font-bold">
                  Change Password
                </header>
                <div class="panel-body">
                  <form class="form-horizontal" id="change_password_form" method="post">						
					<div class="form-group">
							<label class="col-sm-2 control-label" for="current_password">Current Password*</label>
						<div class="col-sm-10">
								<input autocomplete="new-password" type="password" placeholder="Required" name="current_password" value="" class="form-control required_field" id="current_password">
						</div>
					</div>
                    <div class="line line-dashed line-lg pull-in"></div> 
					<div class="form-group">
							<label class="col-sm-2 control-label" for="new_password">New Password*</label>
						<div class="col-sm-10">
								<input autocomplete="off" type="password" placeholder="Required" name="new_password" value="" class="form-control required_field" id="new_password">
						</div>
					</div>
                    <div class="line line-dashed line-lg pull-in"></div> 
					<div class="form-group">
							<label class="col-sm-2 control-label" for="confirm_password">Confirm Password*</label>
						<div class="col-sm-10">
								<input autocomplete="off" type="password" placeholder="Required" name="confirm_password" value="" class="form-control required_field" id="confirm_password">
						</div>
					</div>                   
                    <div class="form-group">
                      <div class="col-sm-4 col-sm-offset-2">                        
						<input type="submit" id="submit" value="Change" class="btn btn-primary">
                      </div>
                    </div>
                  </form>
                </div>
              </section>

  <?php
    include_once('footer.php');
   ?>
 <script>
	 
	 
	 $('#submit').on('click',function(event) {
		 msg='';
		 $('.required_field').each(function(){		 
			if ($(this).val()=='' || $(this).val()==null) {				
				$(this).parent().parent().addClass('has-error');
				lbl_txt = $(this).parent().parent().find('label').text();
				lbl_txt = lbl_txt.replace("Choose file","");
				msg+=lbl_txt+' is required \n';			
			}else{
				$(this).parent().parent().removeClass('has-error');
			}
		 });
		 
		 if (msg.length>0) {
			 alert(msg);
			 event.preventDefault();
		 }else{
			 $('#change_password_form').submit();
		 }
		 		 
	 });
	  	 
 </script>