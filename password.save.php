
<?php // The user is redirected here from login.php.
	include_once 'connect.php'; //Connect to database 
?>
<!DOCTYPE html>
<html lang="en">
	<head>

		<title>Profile</title>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="style.css"/>
		<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
		<script class="jsbin" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
		<script class="jsbin" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.0/jquery-ui.min.js"></script>


	</head> 
	<body>
			<article class='article-password'>
							
					<h1> Reset password </h1>
						

									<form action ='' method ='POST'>
												<input type = 'password' name= 'password1' id = 'password1' placeholder='Enter password'/>	
												<input type = 'password' name= 'password2' id = 'password2' placeholder='Confirm  password'/>
												<button type='submit' name='checkSubmit'> <i class='material-icons'>save</i> </button>
									</form>	

											<?php

											if(isset($_POST['checkSubmit'])){
														$uid = $_SESSION['user_id'];
														$pass1 = sha1($_POST ['password1']);
														$pass2 = sha1($_POST ['password2']);

														if ($pass1 == $pass2){
														
															$query = "UPDATE users SET user_password = '$pass1' WHERE user_id = '$uid';";
															$result  =@mysqli_query($dbc, $query);
															if($result){
																echo 'password uploaded successfully!';
															}else{
																echo 'password not uploaded';	
															}
																		
														}else{
															echo 'password does not match!';
														}
													}		
															
								?>
		</article>
	</body>
</html>