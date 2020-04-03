<?php // The user is redirected here from login.php.
if (session_status() == PHP_SESSION_NONE){    
	session_start(); 

	include_once 'connect.php'; //Connect to database 
	include 'aboutme.inc.php';
}
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
									
						<article class="article-aboutme">
							<h1> About me </h1>
							<?php  
								$id =$_SESSION['user_id'];
								$sql ="SELECT * FROM users WHERE user_id ='$id';";
								$result  = mysqli_query($dbc, $sql);
								while($row = mysqli_fetch_assoc($result)){
									echo "
										<table>
										 <tr>
										    <th></th>
										    <th><a id='button'><i class='material-icons'>edit</i></a></th> 
										  </tr>
										  <tr>
										    <td><p>First Name: </p></td>
										    <td>".$row['user_name']."</td> 
										  </tr>
										  <tr>
										 	 <td><p>Surname: </p></td>
										    <td>".$row['user_surname']."</td> 
										  </tr>
										   <tr>
										 	 <td><p>Email: </p></td>
										    <td>".$row['user_email']."</td> 
										  </tr>
										   <tr>
										 	 <td><p>Phone: </p></td>
										    <td>".$row['user_phone']."</td> 
										  </tr>
										   <tr>
										 	 <td><p>Date of Birth: </p></td>
										    <td>".$row['user_dob']."</td> 
										  </tr>
										   <tr>
										 	 <td><p>Description: </p></td>
										    <td>".$row['user_description']."</td> 
										  </tr>
										   <tr>
										 	 <td><p>Gender: </p></td>
										    <td>".$row['user_gender']."</td> 
										  </tr>
										  <tr>
										 	 <td><p>Location: </p></td>
										    <td>".$row['user_location']."</td> 
										  </tr>
										</table>
									";
								}
							
								if(isset($_SESSION['user_id'])){
										echo "
											<div class='bg-modal-aboutme'>
												<div class='modal-contents-aboutme'>
													<div class='close'>+</div>
															<form action ='".editAboutme($dbc)."' method ='POST'>
																	<input type='hidden' name='uid' value='".$_SESSION['user_id']."'>
																	<input type = 'text' name= 'firstname' id = 'firstname' placeholder='First name'/> 
																	<input type = 'text' name= 'surname' id = 'surname' placeholder='Surname'/> 
																	<input type = 'text' name= 'phone' id = 'phone' placeholder='Phone number'/> 
																	<input type = 'date' name= 'dob' id = 'dob' /> 
																	<textarea name='description'placeholder='Description...' rows = '5'></textarea>
																	<input type = 'text' name= 'location' id = 'location' placeholder='Location'/> 
																	<button type='submit' name='aboutSubmit' class='button-comment'> <i class='material-icons'>save</i> </button>
															</form>
													</div>
											</div>
												";
									}
							?>		
						</article>
				</section>	
			</div>
						<script>
								document.getElementById('button').addEventListener("click", function() {
									document.querySelector('.bg-modal-aboutme').style.display = "flex";
								});

								document.querySelector('.close').addEventListener("click", function() {
									document.querySelector('.bg-modal-aboutme').style.display = "none";
								});
						</script>
		</body>	
</html>	