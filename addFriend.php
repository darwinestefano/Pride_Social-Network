<?php
if (session_status() == PHP_SESSION_NONE){    
	session_start(); 
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


if($_SERVER["REQUEST_METHOD"] == "POST"){
		
	/***************** Code to add a friend ****************/
	if(isset($_POST["friendId"])) { 
		require ('connect.php'); //connect to the database
		
		$friendId = $_POST["friendId"];
		
		//Check if users are not already following each other
		$qCheck = "SELECT * FROM friend WHERE friend_user_id = $userid AND friend_friend_id = $friendId";
		$rCheck = @mysqli_query($dbc, $qCheck);

		if($rCheck == null){	//if db is empty
			$num = 0;			
		} else {
			$num = mysqli_num_rows($rCheck);
			echo "Numero de rows: $num";
		}		

		if($num == 0){	//if they're not friends yet		

			//Add user id and friend id (friend_user_id, friend_main_user_id respectively)
			$qUserFriend = "INSERT INTO friend (friend_user_id, friend_friend_id) VALUES ('$userid', '$friendId')";
			$rUserFriend = @mysqli_query ($dbc, $qUserFriend); // Run the query.

			if ($rUserFriend) { 
				//add the user to the friend's list as well
				$qFriendUser = "INSERT INTO friend (friend_user_id, friend_friend_id) VALUES ('$friendId', '$userid')";
				$rFriendUser = @mysqli_query ($dbc, $qFriendUser); // Run the query.	

			} else { 	
				//echo = "Sorry. A problem occurred.<br/>Please try again.";
				// Debugging message: Don't do this in a live website
				echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $qUserFriend . '</p>';
			} 
		}
	}
}	
?>