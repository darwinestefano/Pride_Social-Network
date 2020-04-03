<?php // The user is redirected here from login.php.
		
function editAboutme($dbc){
if (isset($_POST['aboutSubmit'])){	

	$uid = $_SESSION['user_id'];
	
		// Check for a first name:	
		if (empty($_POST['firstname'])) {
			$errors[] = 'You forgot to enter your first name.';
		} else {
			if(!is_numeric($_POST["firstname"])){
				$fn = trim($_POST['firstname']);
			}else{
				$errors[] = "Please input a valid name. No numeric characters allowed.";
			}
		}
		
		// Check for a  surname:
		if (empty($_POST['surname'])) {
			$errors[] = 'You forgot to enter your last name.';
		} else {
			if(!is_numeric($_POST["surname"])){
				$ln = trim($_POST['surname']);
			}else{
				$errors[] = "Please input a valid surname. No numeric characters allowed.";
			}		
		}	

		// Check for a  phone:
		if (empty($_POST['phone'])) {
			$errors[] = 'You forgot to enter your phone.';
		} else {
			if(is_numeric($_POST["phone"])){
				$ph = trim($_POST['phone']);
			}else{
				$errors[] = "Please input a valid phone.";
			}		
		}	
		
		//Check for date of birth
		if(empty($_POST["dob"])){
			$errors[] = "Please input your <b>date of birth</b>.";
		}else{
			$dob = trim($_POST["dob"]);
		}

		// Check for a first name:	
		if (empty($_POST['description'])) {
			$errors[] = 'You forgot to enter your description.';
		} else {
			if(!is_numeric($_POST["description"])){
				$ds = trim($_POST['description']);
			}else{
				$errors[] = "Please describe yourself.";
			}
		}
		// Check for a first name:	
		if (empty($_POST['location'])) {
			$errors[] = 'You forgot to enter your location.';
		} else {
			if(!is_numeric($_POST["location"])){
				$loc = trim($_POST['location']);
			}else{
				$errors[] = "Please describe yourself.";
			}
		}
		if (isset($errors) && !empty($errors)) {	
			echo '<p class="error">';
			foreach ($errors as $msg) {
				echo "$msg";
			}
			echo '</p>';
		}
				if (empty($errors)) { // If everything's OK.

			// Make query data save
			$fn = mysqli_real_escape_string($dbc, trim($fn));
			$ln = mysqli_real_escape_string($dbc, trim($ln));	
			$ph = mysqli_real_escape_string($dbc, trim($ph));
			$dob = mysqli_real_escape_string($dbc, trim($dob));
			$ds = mysqli_real_escape_string($dbc, trim($ds));
			$loc = mysqli_real_escape_string($dbc, trim($loc));

			// Make the query:
			$sql = "UPDATE users SET user_name ='$fn', user_surname ='$ln', user_phone ='$ph', user_dob ='$dob', user_description ='$ds', user_location ='$loc' WHERE user_id ='$uid';";

			$r = @mysqli_query ($dbc, $sql); // Run the query.

			if ($r){ // If it ran OK.
			
				header("Location: profile.php");

			} else{	// If it did not ran OK.
				echo 'System Error<br/>You could not add new Student due to a system error. We apologize for any inconvenience.'; 	
															//////******************** Debugging message: Don't do this in a live website
				echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $sql . '</p>';
			}
		}
	}
}