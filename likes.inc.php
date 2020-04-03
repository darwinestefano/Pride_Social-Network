<?php
//function to set likes (duplicates not allowed)
function setLikes($dbc, $feedId, $userId){ 
	// Make the check query 
	$qCheck = "SELECT * FROM likes WHERE likes_feed_id = $feedId AND likes_user_id = $userId";
	$rCheck = @mysqli_query($dbc, $qCheck);

	if(mysqli_num_rows($rCheck) == 0){	//if user hasn't liked the feed yet, then set the like

		$q = "INSERT INTO likes (likes_feed_id, likes_user_id) VALUES ('$feedId', '$userId')";	

		$r = @mysqli_query ($dbc, $q); // Run the query.
		
		if ($r) { 
			//if 'like' is successfully saved, reload at success response		

		} else { 	
			$error = "Sorry. A problem occurred.<br/>Please try again.";
			// Debugging message: Don't do this in a live website
			//echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $q . '</p>';
		} 
	}
}


//function to get the likes from each feed
function getLikes($dbc, $feedId){
	$friendsName = array();		//array to store friend's name
	
	$qLikes = "SELECT likes_user_id, user_name, user_surname FROM likes 
	INNER JOIN users ON likes_user_id = user_id WHERE likes_feed_id = $feedId";
							
	$rLikes = @mysqli_query ($dbc, $qLikes); //run the query
	$totalReturned = mysqli_num_rows($rLikes);    //returs number of likes
															
	if ($totalReturned > 0) {
		//store friend's name and id into the array
		while($results = mysqli_fetch_array($rLikes)){
			$username = $results['user_name'] . " " . $results['user_surname'];
			$friendsName[$results['likes_user_id']] = $username;
		}
	}	
	return $friendsName;
}


?>