<?php 
if (session_status() == PHP_SESSION_NONE) {    
	session_start(); 
}
if (isset($_SESSION['user_id'])) {
	$_SESSION = array(); 	// Clear the variables.
	session_destroy(); 		// Destroy the session itself.
	setcookie ('PHPSESSID', '', time()-3600, '/', '', 0, 0); // Destroy the cookie.
	$url = "index.php";
	header("Location: $url");	//redirect to index.php
	exit();
}
?>