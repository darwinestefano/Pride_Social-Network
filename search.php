<?php // The user is redirected here when search button is clicked on the header
if (session_status() == PHP_SESSION_NONE){    
	session_start(); 

	include 'connect.php'; //Connect to database 
}
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Pride - Search</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="styles-home.css"/>
		<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
		<script class="jsbin" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
		<script class="jsbin" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.0/jquery-ui.min.js"></script>
	</head>
	<body>
	<?php include 'header.html';	

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
	?>
	
	<div class= "profile-page">
		<aside class="search-results">
	
			<?php 
			//function to validate user's input (security)
			function validate_input($data){
				$data = trim($data);
				$data = htmlspecialchars($data);
				return $data;
			}

			if($_SERVER["REQUEST_METHOD"] == "GET"){
				// Check for a valid query in the URL:
				if ((isset($_GET['search'])) && (!is_numeric($_GET['search']))) {
					$query = $_GET['search'];	
				} 

				if($query == ""){
					echo "<p class=\"search-message\">Please type the name of your friend above.</p>";
				}
				else{	
					$query = validate_input($_GET['search']);  
					$query = mysqli_real_escape_string($dbc, $query); // makes sure nobody uses SQL injection
					
					//fetch all ids of friends into an array
					$friendIdArray = array();
					
					$qFetchFriends = "SELECT friend_friend_id FROM friend WHERE friend_user_id = $userid";
					$rFetchFriends = @mysqli_query ($dbc, $qFetchFriends);
					$numFriends = mysqli_num_rows($rFetchFriends);
					
					if($numFriends > 0){
						while($row = mysqli_fetch_array($rFetchFriends)){
							array_push($friendIdArray, $row['friend_friend_id']);
						}
					}		
					
					//spliting the query if user types name and surname
					$names = explode(" ", $query);
					$namesLength = count($names);			
				
					$firstName = $names[0];				//get the first input to search for first name
					$surname = $names[$namesLength-1];	//get last input to search for surname
					
					$q = "SELECT * FROM users WHERE (user_id != '$userid') AND ((user_name LIKE '$firstName%') OR (user_surname LIKE '$surname%'))";
					$r = @mysqli_query ($dbc, $q);
					$num = mysqli_num_rows($r);
						
					if($num > 0){ // if one or more rows are returned
							
						echo "<p class='search-total'>Your search returned $num results.</p>";				 

						while($results = mysqli_fetch_array($r)){  
							$friend_username = $results['user_name'] ." ". $results['user_surname'];
							$friend_id = $results['user_id'];
							
							//check if friend is already in friend's list
							$isFriend = in_array($friend_id, $friendIdArray);
							
							echo "<div class='search-list'>";
							$path = "uploads/profile". $friend_id.".jpg";		
							if(file_exists($path)){ //if user has a profile image, display it, otherwise display a default profile img
								echo "<img src ='uploads/profile". $friend_id.".jpg' alt='profile image' class ='search-image-friends' width='90' height='90'>";
							}else{
								echo "<img src ='uploads/profiledefault.jpg' alt='profile image' class ='search-image-friends' width='90' height='90'>";
							}
							echo "<span class='search-username'><a href='profile.php?id=$friend_id'>$friend_username</a></span>";
							
							if($isFriend){
								echo "<i class='myBtn-done material-icons'>done_outline</i></div>";
							}else{
								echo "<button class='myBtn-add' value='".$results['user_id']."'><i class='material-icons'>people</i>+</button></div>";
							}	
						}
					}
					else{ // if there is no matching rows
						echo "<p class=\"search-message\">Sorry. Your search returned $num results.</p>";
					}  
				}  
			} 
			?>
		</aside>

<script> 
	/********** Code to Add/follow a friend **********/
	$(document).ready(function(){
		$('.myBtn-add').click(function(e){				//when '+' button is clicked
			e.preventDefault();
			var clickBtnValue =  $(this).val();; 	//get the friend id
			data =  {'friendId': clickBtnValue};
			$.post("addFriend.php", data).done(function() {
           	 	function startRefresh() {
    				$.get('', function(data) {
       				 	$(document.body).html(data);    
   					 });
				}
				$(function() {
   					setTimeout(startRefresh,1000);
				});
  			})
  			.fail(function() {
    			alert( "An error occurred. Please try again." );
  			});
		});
	});
</script>
</div>
</body>	
</html>	