<?php 
	if (session_status() == PHP_SESSION_NONE){    
		session_start(); 
		include 'connect.php';
	}

	// If no session value is present, redirect the user:
	// Also validate the HTTP_USER_AGENT!
	if (!isset($_SESSION['agent']) OR ($_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT']) )) {	

		require ('login_functions.inc.php');
		redirect_user();	
	}
	if (!isset($_SESSION['user_id'])){
		require ('login_functions.inc.php');
		redirect_user();
	}

	//variables
	$userid = $_SESSION['user_id'];
	$username = $_SESSION['user_name'] . " " . $_SESSION['user_surname'];
	$uname  = $_REQUEST['uname'];
	$msg = $_REQUEST['msg'];
	
	$qu = "SELECT*FROM users WHERE user_id = '$uname';";	
	$res = @mysqli_query ($dbc, $qu); 
	if($row = mysqli_fetch_array($res)){
		$friendname = $row['user_name'];
	}

	// Register the user and message in the database...
		
		$time = date("H:i:s");
		
		// Make the insert query
		$q = "INSERT INTO logs (logs_userid, logs_friendid, logs_message, logs_time) VALUES ('$userid','$uname', '$msg', '$time')";		
		$r = @mysqli_query ($dbc, $q); // Run the query.
		
		$query = "SELECT*FROM logs WHERE (logs_userid = $userid AND logs_friendid = $uname) OR (logs_userid = $uname AND logs_friendid = $userid) ORDER by logs_id ASC";		
		$result = @mysqli_query ($dbc, $query); // Run the query.

		if ($result) { // If it ran OK, display the records.

			// Count the number of returned rows:
			$num = mysqli_num_rows($result);

			if ($num > 0) { // if records returned
				// Fetch and print all the records:
				while ($row = mysqli_fetch_array($result)) {
					
					if($row['logs_userid'] != $userid){		//friend's reply -> display right
						echo '<div class="chatbox-right">
							<span class="chatbox-username-right">'.$friendname.'</span><br/>
							<span class="chatbox-time-right">'.$row['logs_time'].'</span><br/>
							<p class="chatbox-msg-right">'.$row['logs_message'].'</p>
						</div>';
					}else{										//user's message --> display left
						echo '<div class="chatbox-left">
						<span class="chatbox-username-left">'.$username.'</span><br/>	
							<span class="chatbox-time-left">'.$row['logs_time'].'</span><br/>
							<p class="chatbox-msg-left">'.$row['logs_message'].'</p>
						</div>';
					}
				}
			}
			else {
				echo "<p class='chatbox-clear'>Start chat...</p>";
			}
		}
	
?>
