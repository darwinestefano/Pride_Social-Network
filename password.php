
<?php // The user is redirected here from login.php.

	session_start(); 

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
		<header>
				<nav>
					<div class="menu-dropdown">
						<a href="" ><i class="material-icons">menu</i></a>
						  <div class="dropdown-content">
							<a href="aboutme.php">About me</a>
							<a href="messeger.php">Messager</a>
							<a href="logout.php">Log out</a>
						  </div>
					</div>
					<a href="home.php"><img src="images/logo.png" alt="Logo" height="30" width="120"></a>
					<form action="" method="POST">
						<input type="text" placeholder="Search Friends" name="search"> 
						<button class ="button-search" type="submit"><i class="material-icons">search</i></button><br/><br/>
					</form>
				</nav>	
		</header>
			<div class= "profile-page">
				<section>
						<aside class="aside-aboutme">		
							<ul class='aboutme-list' >
								<li><a href="aboutme.php">About me</a></li>
								<li><a href="password.php" >Password</a></li>
							</ul>	
									
						</aside>
									
						<article class="article-password">
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
							<?php  
							
							if (isset($_POST['submit'])) {	
								
								$errors = array(); // Initialize an error array.
								
								$uid = $_SESSION['user_id'];
								$query = "SELECT* FROM users WHERE user_id ='$uid';";
								$result  =@mysqli_query($dbc, $query);
					
									if($row = mysqli_fetch_assoc($result)){
										
										$emailSession = $row['user_email'];
										$passwordSession = $row['user_password'];
									}
								$email = $_POST['email'];
								
								$password = sha1($_POST['password']);
							
								if($emailSession == $email && $passwordSession == $password){
								
										echo "<button name='edit' name='edit' id='btn-edit'> <i class='material-icons'>edit</i> </button>";

								} else { // Unsuccessful!
										
										echo " <p>Password does not match!</p>";

										$errors[0] = 'Sorry, email address and password entered do not match. <br/><br/>Please try again.';
									} 
								}else{
									// Check for an email address:
									if (empty($_POST['email'])) {
										$errors[] = 'You forgot to enter your email address.';
									} else {
										$e = trim($_POST['email']);
										if(!filter_var($e, FILTER_VALIDATE_EMAIL)){
											$errors['email']  = "Invalid email format.";
										}		
									}
									
									// Check for a password 
									if (empty($_POST['password'])) {
										$errors['password'] = 'You forgot to enter your password.';
									} else {
										$p = trim($_POST['password']);
									}
								} 
							?>		

					
							<h1> Reset password </h1>
							<form action ="" method ="POST">
									<input type = "email" name="email" id ="email" placeholder="Please informe your currently email"/> 
									<?php if(isset($data['email'])){ echo "<p class='err'>". $data['email'] . "</p>";} ?>
									<input type = "password" name= 'password' id="password" placeholder="Enter your password"/> 
									<?php if(isset($data['password'])){ echo "<p class='err'>". $data['password'] . "</p>";} ?>
									<button name="submit" name="submit" id="btn"> <i class="material-icons">update</i> </button>
							</form>
					
							<form action ='' method ='POST' id ='MyForm'>
									<input type = 'password' name= 'password1' id = 'password1' placeholder='Enter password'/>	
									<?php if(isset($data['passwordSubmitted'])){ echo "<p class='err'>". $data['password'] . "</p>";} ?>
									<input type = 'password' name= 'password2' id = 'password2' placeholder='Confirm  password'/>
									<?php if(isset($data['passwordSubmitted'])){ echo "<p class='err'>". $data['password'] . "</p>";} ?>
									<button type='submit' name='checkSubmit'> <i class='material-icons'>save</i> </button>
							</form>	
											
							

						</article>
				</section>	
				<script>
				$("#btn-edit").click(function ( event ) { 
  					  $("#MyForm").show();
   					 event.preventDefault();
					});
			</script>
			</div>
			
		</body>	
</html>	