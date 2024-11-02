<!DOCTYPE html>
<html lang="en">
<?php 
session_start();
include('./db_connect.php');
ob_start();
if(!isset($_SESSION['system'])){
	// $system = $conn->query("SELECT * FROM system_settings limit 1")->fetch_array();
	// foreach($system as $k => $v){
	// 	$_SESSION['system'][$k] = $v;
	// }
}
ob_end_flush();
?>
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Gym Management System</title>
 	

<?php include('./header.php'); ?>
<?php 
if(isset($_SESSION['login_id']))
header("location:index.php?page=home");

?>

</head>
<style>
	body{
		width: 100%;
	    height: calc(100%);
	    /*background: #007bff;*/
	}
	#login-right{
		position: absolute;
		right:0;
		width:40%;
		height: calc(100%);
		background:white;
		display: flex;
		align-items: center;
		
	}
	#login-left{
		position: absolute;
		left:0;
		width:60%;
		height: calc(100%);
		background:#59b6ec61;
		display: flex;
		align-items: center;
		background: url(assets/uploads/<?php echo $_SESSION['system']['cover_img'] ?>);
	    background-repeat: no-repeat;
	    background-size: cover;
	}
	#login-right .card{
		margin: auto;
		z-index: 1;
		border-radius: 20px;
		height: 450px;
		background: linear-gradient(to right, #000000, #333333);
		backdrop-filter: blur(10px); /* Adds the blur effect */
    	box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2); 
	}
	label,h1{
		color: white;
	}
	.logo {
    margin: auto;
    font-size: 8rem;
    background: white;
    padding: .5em 0.7em;
    border-radius: 50% 50%;
    color: #000000b3;
    z-index: 10;
}
div#login-right::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: calc(100%);
    height: calc(100%);
	background: linear-gradient(to left, #000000, #ffffff);

	
}
.cover-photo{
		width: 100%;
		height: 100%;
	}
input::placeholder {
    text-align: center;
}
#password{
	border-radius: 30px;
}
#username{
	border-radius: 30px;
}
#btn-login{
	box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2); /* Optional shadow for depth */
    backdrop-filter: blur(10px); /* Adds the blur effect */
    z-index: 1; 
	width: 150px;
	margin-top: 30px;
	font-weight: bold;
	border: none;
	height: 40px;
	background-color: red;
	transition: all 500ms;
}
#btn-login:hover{
	border: 1px solid;
	border-color: black;
	border-radius: 20px;
	color: black;
	background-color: white;
	letter-spacing: 2px;
}
#login-form{
	margin-top: 40px;
}

</style>

<body>



  		<div id="login-left">
			<img class="cover-photo" src="motiv.jpg" alt="error">
  		</div>

  		<div id="login-right">
  			<div class="card col-md-8">
  				<div class="card-body" id="card-body">
  					<form id="login-form" >
					  <div id="user-txt" style="height: 20px;"><center><h1>User Login</h1></center></div><br><br><br>
  						<div class="form-group">
  							<label for="username" class="control-label">Username:</label>
  							<input type="text" id="username" name="username" class="form-control" placeholder="Enter Username">
  						</div>
  						<div class="form-group">
  							<label for="password" class="control-label">Password:</label>
  							<input type="password" id="password" name="password" class="form-control" placeholder="Enter Password">
  						</div>
  						<center><button id="btn-login" class="btn-sm btn-block btn-wave col-md-4 btn-primary" style="max-width: 700px !important">Login</button></center>
  					</form>
  				</div>
  			</div>
  		</div>
   



  <a href="#" class="back-to-top"><i class="icofont-simple-up"></i></a>


</body>
<script>
	$('#login-form').submit(function(e){
		e.preventDefault()
		$('#login-form button[type="button"]').attr('disabled',true).html('Logging in...');
		if($(this).find('.alert-danger').length > 0 )
			$(this).find('.alert-danger').remove();
		$.ajax({
			url:'ajax.php?action=login',
			method:'POST',
			data:$(this).serialize(),
			error:err=>{
				console.log(err)
		$('#login-form button[type="button"]').removeAttr('disabled').html('Login');

			},
			success:function(resp){
				if(resp == 1){
					location.href ='index.php?page=home';
				}else{
					$('#login-form').prepend('<div style="position:absolute;" class="alert alert-danger">Username or password is incorrect.</div>')
					$('#login-form button[type="button"]').removeAttr('disabled').html('Login');
				}
			}
		})
	})
</script>	
</html>