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

require ('connect.php'); //connect to the database


/*********************** DELETE FEED *****************************/
// Check for a valid feed ID in the URL:
if((isset($_GET['feedid'])) && (is_numeric($_GET['feedid']))) {
	$feedId = $_GET['feedid'];
	
	//Delete all likes linked to the feed (if any)
	$qDeleteLikes = "DELETE FROM likes WHERE likes_feed_id = $feedId";
	$rDeleteLikes = @mysqli_query($dbc, $qDeleteLikes);

	//Delete all comments linked to the feed
	$qDeleteComments = "DELETE FROM comments WHERE comment_feed_id = $feedId";
	$rDeleteComments = @mysqli_query($dbc, $qDeleteComments);

	//Make the query for deleting feed
	$q = "DELETE FROM feed WHERE feed_id=$feedId LIMIT 1";		
	$r = @mysqli_query ($dbc, $q);
	if (mysqli_affected_rows($dbc) == 1) { // If it ran OK.
		//redirect to home.php
		header('Location: home.php');
	} else { 
		$error = "The feed could not be deleted due to a system error.";
		echo '<p>' . mysqli_error($dbc) . '<br />Query: ' . $q . '</p>'; // Debugging message.
	}
} 


/*********************** DELETE COMMENT *****************************/
// Check for a valid comment ID in the URL:
if((isset($_GET['commentid'])) && (is_numeric($_GET['commentid']))) {
	$commentId = $_GET['commentid'];
	
	//Make the query for deleting comment
	$q = "DELETE FROM comments WHERE comment_id = $commentId LIMIT 1";		
	$r = @mysqli_query ($dbc, $q);
	if (mysqli_affected_rows($dbc) == 1) { // If it ran OK.
		//redirect to home.php
		header('Location: home.php');
	} else { 
		$error = "The comment could not be deleted due to a system error.";
		echo '<p>' . mysqli_error($dbc) . '<br />Query: ' . $q . '</p>'; // Debugging message.
	}
} 


mysqli_close($dbc); // Close the database connection.		
exit(); // quit the script
?>