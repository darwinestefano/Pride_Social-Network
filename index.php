<?php
if (session_status() == PHP_SESSION_NONE){    
	session_start(); 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>	
	<title>Pride</title>
	<meta charset="utf-8" />
	<link rel="stylesheet" href="style-index.css" />
	<link href="https://fonts.googleapis.com/css?family=Libre+Franklin|Rajdhani|Great+Vibes|Anton|Francois+One|Playfair+Display+SC" rel="stylesheet">
	<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
</head>
 <body> 
 
 <?php		
if ($_SERVER['REQUEST_METHOD'] == 'POST') {	
	require ('connect.php');		
	require ('login_functions.inc.php');	
	
	$errors = array(); // Initialize an error array.

	// Check the login: validates the form data, queries the db with the email & password
	// returns true/false variable & array of errors or database result
	if(isset($_POST['login']) && isset($_POST['password'])){
		
		list ($check, $data) = check_login($dbc, $_POST['login'], $_POST['password']);
		
		if ($check) { // Valid username and password

			$_SESSION['user_id'] = $data['user_id'];
			$_SESSION['user_name'] = $data['user_name'];
			$_SESSION['user_surname'] = $data['user_surname'];
		
			// Store the HTTP_USER_AGENT: 
			// Extra layer of security to help prevent session hacking.
			// This is a combination of the browser and operating system
			// Could only hack into user's session if they were running 
			// exact same browser and exact same operating system.
			$_SESSION['agent'] = md5($_SERVER['HTTP_USER_AGENT']);

			// Redirect:				
			redirect_user('home.php');
			
		} 
	}
	else{		//Check the registration form			
	
		// Check for a first name:	
		if (empty($_POST['firstname'])) {
			$errors['firstname'] = 'You forgot to enter your first name.';
		} else {
			if(!is_numeric($_POST["firstname"])){
				$fn = trim($_POST['firstname']);
			}else{
				$errors['firstname'] = "Please input a valid name. No numeric characters allowed.";
			}
		}
		
		// Check for a last name:
		if (empty($_POST['surname'])) {
			$errors['surname'] = 'You forgot to enter your last name.';
		} else {
			if(!is_numeric($_POST["firstname"])){
				$ln = trim($_POST['surname']);
			}else{
				$errors['surname'] = "Please input a valid surname. No numeric characters allowed.";
			}		
		}	
			
		// Check for an email address:
		if (empty($_POST['email'])) {
			$errors['email'] = 'You forgot to enter your email address.';
		} else {
			$e = trim($_POST['email']);
			if(!filter_var($e, FILTER_VALIDATE_EMAIL)){
				$errors['email']  = "Invalid email format.";
			}		
		}
		
		// Check for a password 
		//Password must be at leats 8 characters length composed of at least 
		//one upper case, one lower case, one special chars and a digit
		if (empty($_POST['password'])) {
			$errors['pass'] = 'You forgot to enter your password.';
		} else {
			$p = trim($_POST['password']);
			
			//Check if it has at least 8 characters
			if(strlen($p) >= 8){ 		//if yes
			
				//check if password has at least one upper case (using REGEX)
				if(preg_match('/[A-Z]/', $p)){
					
					//check if password has at least one lower case (using REGEX)
					if(preg_match('/[a-z]/', $p)){
						
						//check if password has at least one special character (using REGEX)
						if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $p)){
							
							//check if password has at least one digit
							if(!preg_match('/\d/', $p)){
								$errors['pass'] = 'Your password must have at least 1 digit.';
							}
						}else{
							$errors['pass'] = 'Your password must have at least 1 special character.';
						}	
					}else{
						$errors['pass'] = 'Your password must have at least 1 lower case character.';
					}
				}else{
					$errors['pass'] = 'Your password must have at least 1 upper case character.';
				}
			}else{
				$errors['pass'] = 'Your password must have at least 8 characters.';
			}
		}
		
		//Check for date of birth
		if(empty($_POST["dob"])){
			$errors['dob'] = "Please input your <b>date of birth</b>.";
		}else{
			$dob = trim($_POST["dob"]);
			$age = date_diff(date_create($dob), date_create('today'))->y; 
			if($age < 18){
				$errors['dob'] = "Sorry, you must be at least 18 years old.";
			}
		}
		
		if (count($errors) == 0) { // If everything's OK.
		
			// Register the user in the database
			
			// Make query data save
			$fn = mysqli_real_escape_string($dbc, trim($fn));
			$ln = mysqli_real_escape_string($dbc, trim($ln));
			$e = mysqli_real_escape_string($dbc, trim($e));
			$p = mysqli_real_escape_string($dbc, trim($p));
			$dob = mysqli_real_escape_string($dbc, trim($dob));
			
			// Make the query:
			$q = "INSERT INTO users (user_name, user_surname, user_email, user_password, user_dob, user_registration_date) 
			VALUES ('$fn', '$ln', '$e', SHA1('$p'),'$dob', NOW())";		
			$r = @mysqli_query ($dbc, $q); // Run the query.
			
			if ($r){ // If it ran OK.
				list ($check, $data) = check_login($dbc, $e, $_POST['password']);
						
				if ($check) { // Valid username and password

					$_SESSION['user_id'] = $data['user_id'];
					$_SESSION['user_name'] = $data['user_name'];
					$_SESSION['user_surname'] = $data['user_surname'];
			
					// Store the HTTP_USER_AGENT: 			
					$_SESSION['agent'] = md5($_SERVER['HTTP_USER_AGENT']);	
											
				}

			} else{	// If it did not ran OK.
				echo 'System Error<br/>You could not add new user due to a system error. We apologize for any inconvenience.'; 	
				//******************** Debugging message: Don't do this in a live website
				echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $q . '</p>';
			}

			$userid = $_SESSION['user_id'];
			$username =$_SESSION['user_name'];
			$email =$_SESSION['email'];
			$password =$_SESSION['password'];
			$sql = "SELECT * FROM users WHERE user_id = '$userid' AND user_name = '$username' ;";
			$result =  mysqli_query($dbc, $sql);
			if($result){
				#create a section that stores user_id, and can be used to give accessibility to user 
				$userid = $_SESSION['user_id'];
				$sql = "INSERT INTO profileimg (profileimg_user_id, profileimg_status) VALUES ('$userid', 1);";
				mysqli_query($dbc, $sql);
			}
		
			
			// Redirect:						
			redirect_user('home.php');		


			mysqli_close($dbc); // Close the database connection.		
			exit(); // quit the script
		}
	}	
}
?>

<div id= "page">
		<table class= "myTable">
			 <thead>
					<tr>
					<th></th>
					<th> <h1>Be proud. Join us ! <h1></th>
					</tr>
			</thead>
			<tbody>
				<tr>
					<td class="td-login">
					<img src="images/pride-logo.jpeg" alt="Avatar">
						<form action ="" method ="POST">
							<input type = "email" name= "login" id = "login" placeholder="username"S />  <br/>
							<?php if(isset($data['email'])){ echo "<p class='err'>". $data['email'] . "</p>";} ?>
							
							<input type = "password" name= "password" id = "password" placeholder="password" />  <br/>
							<?php if(isset($data['pass'])){ echo "<p class='err'>". $data['pass'] . "</p>";} ?>
							
							<?php if(isset($data['match'])){ echo "<p class='err'>". $data['match'] . "</p>";} ?>
							
							<input type="submit" value ="LOGIN">
						</form>
					</td>
					<td>
						<form action ="" method ="POST">
							<input type = "text" name= "firstname" id = "firstname" placeholder="First name"/>  <br/>
							<?php if(isset($errors['firstname'])){ echo "<p class='err'>". $errors['firstname'] . "</p>";} ?>
							
							<input type = "text" name= "surname" id = "surname" placeholder="surname"/>  <br/>
							<?php if(isset($errors['surname'])){ echo "<p class='err'>". $errors['surname'] . "</p>";} ?>
							
							<input type = "email" name= "email" id = "email" placeholder="email"   /> <br>
							<?php if(isset($errors['email'])){ echo "<p class='err'>". $errors['email'] . "</p>";} ?>
							
							<input type = "password" name= "password" id = "password" placeholder="password" 
							title="Your password must have at least 8 characters,&#10;an upper case, lower case, special char and a digit."/>  <br/>
							<?php if(isset($errors['pass'])){ echo "<p class='err'>". $errors['pass'] . "</p>";} ?>
							
							<input type = "date" name= "dob" id = "dob" />  <br>
							<?php if(isset($errors['dob'])){ echo "<p class='err'>". $errors['dob'] . "</p>";} ?>
							<input type="submit" value ="SIGN UP">
						</form>
					</td>
				</tr>
			</tbody>
		</table>
</div>
 </body>
 </html> 

