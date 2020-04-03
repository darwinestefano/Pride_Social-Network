<?php // The user is redirected here from index.php (after logged in)
if (session_status() == PHP_SESSION_NONE){    
	session_start(); 
}
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Pride - Home</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="styles-home.css"/>
		<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons"> 		

		<!-- JQuery -->
		<script class="jsbin" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
		<script class="jsbin" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.0/jquery-ui.min.js"></script>
		<!-- Bootstrap for modals -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script> 
	</head>
	<body>
	
<?php 
include 'header.html';
require 'likes.inc.php'; 	//functions for 'likes'
require 'connect.php';	 	//connect to the database

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
$error = "";
$fileDestination = "";
$uploadOk = 0;
$numberOfLikes = "";
$friendsName = array();

//function to validate user's input (security)
function validate_input($data){
	$data = trim($data);
	$data = htmlspecialchars($data);
	return $data;
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
		
	/***************** SET LIKES (likes.inc.php) ****************/
	if (isset($_POST["feedId"])) { 
		$friendsName = setLikes($dbc, $_POST["feedId"], $userid);
		$numberOfLikes = sizeof($friendsName);
	}
	
	/***************** SET COMMENTS ****************/
	if (isset($_POST['commentSubmit'])) {
		$fid = $_POST['fid'];
		$uid = $_POST['uid'];
		$message = $_POST['message'];

		$sql = "INSERT INTO comments (comment_feed_id, comment_user_id, comment_date, comment_comment) 
		VALUES ('$fid','$uid',NOW(), '$message')";
		$result = @mysqli_query ($dbc, $sql);
	}
	
	/************* SAVE NEW FEED ****************/
	//Get feed post (if any)
	if(!empty($_POST["feed"])){
		$feedText = validate_input($_POST["feed"]);
		$feedText = mysqli_real_escape_string($dbc, $feedText); // makes sure nobody uses SQL injection
	}else{
		$feedText = "";
	}
	
	//Get feed image (if any)
	if(isset($_FILES["uploadPost"])){    
		//Set default name for a file to generate an unique file
		$newFileName = $_SESSION['user_id'] . basename($_FILES["uploadPost"]["name"]);		
		if(empty($newFileName)){
			$newFileName =""; //if there is no image selected
		}else{
			$newFileName = strtolower(str_replace(" ", "-",$newFileName)); 
		}

		//Get the actually image 
		$file = $_FILES["uploadPost"]["tmp_name"];
		if($file){
			//Get variables from image , like name, type, temp name, error and size
			$fileName = $_FILES["uploadPost"]["name"];
			$fileType = $_FILES["uploadPost"]["type"];
			$fileTempName = $_FILES["uploadPost"]["tmp_name"];
			$fileError = $_FILES["uploadPost"]["error"];
			$fileSize = $_FILES["uploadPost"]["size"];

			//Get extension from the image , explode and set as lower case 
			$fileExt = explode(".",$fileName);
			$fileActualExt = strtolower(end($fileExt));

			//Set allowed extensions 
			$allowed = array("jpg","jpeg","png");

			//check if extension is allowed 
			if(in_array($fileActualExt, $allowed)){
				//Check if file uploaded properly
				if($fileError === 0){
					//Check file size in kb
					if($fileSize <2000000){
						//Save in the  file system 
						$imageFullName = $newFileName.".".uniqid("",true).".".$fileActualExt;
						$fileDestination = 'img/feeds/'.$imageFullName; 
						$uploadOk = 1;
					}else{
						$error = "File exceeded limit size!";   
					}
				}else{
					$error =  "You had an error! Please try again."; 
				}
			}else{
				$error = "You need to upload a proper file extension";
			}
		}
	}	
	
	//if no errors, save feed to db 
	if($error == ""){		
		if(!$fileDestination == "" || !$feedText == ""){
			if ($uploadOk == 1) {
				move_uploaded_file($_FILES["uploadPost"]["tmp_name"], $fileDestination);		
			} 	
			
			// Make the insert query
			$q = "INSERT INTO feed (feed_user_id, feed_date_time, feed_message, feed_photo) 
			VALUES (".$_SESSION['user_id'].", NOW(), '$feedText', '$fileDestination')";		
			$r = @mysqli_query ($dbc, $q); // Run the query.
				
			if (!$r) {
				// Debugging message: Don't do this in a live website
				echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $q . '</p>';
			}
		}	
	}
	
	
	/************* UPDATE FEED ****************/
	if(isset($_POST['feed_update'])){
		$feed_updated = validate_input($_POST['feed_update']);			//get the new message
		$feed_updated = mysqli_real_escape_string($dbc, $feed_updated); // makes sure nobody uses SQL injection
		
		$feedId_toEdit = $_POST['feed_id_update']; 	//get the id of the feed to be updated
		
		$qUpdateFeed = "UPDATE feed SET feed_message = '$feed_updated' WHERE feed_id = $feedId_toEdit";
		$rUpdateFeed = @mysqli_query ($dbc, $qUpdateFeed); // Run the query
		
		if (!$rUpdateFeed) {
			// Debugging message: Don't do this in a live website
			echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $qUpdateFeed . '</p>';
		}
	}	
	
	/************* UPDATE COMMENT ****************/
	if(isset($_POST['comment_update'])){
		$comment_updated = validate_input($_POST['comment_update']);			//get the new message
		$comment_updated = mysqli_real_escape_string($dbc, $comment_updated); // makes sure nobody uses SQL injection
		
		$commentId_toEdit = $_POST['comment_id_update']; 	//get the id of the comment to be updated
		
		$qUpdateComment = "UPDATE comments SET comment_comment = '$comment_updated', comment_date = NOW() WHERE comment_id = $commentId_toEdit";
		$rUpdateComment = @mysqli_query ($dbc, $qUpdateComment); // Run the query
		
		if (!$rUpdateComment) {
			// Debugging message: Don't do this in a live website
			echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $qUpdateComment . '</p>';
		}
	}
}
?>

<div class= "profile-page">
	<!-- Left side -->
	<section class="home-left">
		<aside class="aside-home">
			<div id="imgContainer">
				<!-- ******************************************************************************************************-->
				<!-- USER PROFILE PICTURE-->
				<!-- ******************************************************************************************************-->
				<?php
				#Profile picture config - This section upload a picture to be set as profile into the database, 
				#otherwise is automatically set a default image when account is created 
					$userid = $_SESSION['user_id'];
					$sql  = "SELECT * FROM users where user_id = '$userid';";
					$result  = mysqli_query($dbc, $sql);

					if(isset($_SESSION['user_id'])){
						if (mysqli_num_rows($result) > 0){
							while($row = mysqli_fetch_assoc($result)){
								//check if user already has an picture uploaded
								$id =$row['user_id'];
								$sqlImg ="SELECT * FROM profileimg WHERE profileimg_user_id ='$id'";
								$resultImg  = mysqli_query($dbc, $sqlImg);

								while($rowImg = mysqli_fetch_assoc($resultImg)){
									 echo"<div class ='user-container'>";

									if($rowImg['profileimg_status'] == 0){
										echo "	<div class='containerImg'>
													<img src ='uploads/profile".$id.".jpg' class='image'>
														<div class='middle'>
															<a id='button' class ='button'>update</a>
														</div>
												</div>
												";
									}else {
										echo "	<div class='containerImg'>
													<img src ='uploads/profiledefault.jpg' class='image'>
														<div class='middle'>
													    	<a id='button' class ='button'>update</a>
													 	</div> 
												 </div>";
									}
									echo "</div>";
								}
							}
						} else {
							echo "There are no users yet";
						}
					}

					if(isset($_SESSION['user_id'])){
						echo "
							<div class='bg-modal'>
								<div class='modal-contents2'>
									<div class='close'>+</div>
										<form action='upload.php' method ='post' enctype ='multipart/form-data'>
											<img img src ='uploads/profiledefault.jpg' class='image' alt='your image'  id='blah'/>
											<input type='file' name='file' id='file' onchange='readURL(this);'>
											<label for='file' class='btn-3'><span>Select an image gorgeous!</span></label>
											<button type= 'submit' name='submit' class='button'> UPLOAD </button>
										</form>
								</div>
							</div>
								";
					}	
				?>								
			</div>	

			<?php					
			echo "<h1>" . $_SESSION['user_name'] ." " . $_SESSION['user_surname'] ."</h1>" ?>
			<ul>
			<li><a href="profile.php" class="home-profile-link">Profile</a></li>
			<li><a href="" class="home-friends"><i class="material-icons">people</i></a></li>
			<li><a href="" class="home-notification"><i class="material-icons">notifications_none</i></a></li>
			</ul>
		</aside>
		
		<!-- ******************************************************************************************************-->
		<!-- DISPLAY FRIENDS -->
		<!-- ******************************************************************************************************-->		
		<aside class="home-friends-list">
			<?php 
			$qGetFriends = "SELECT user_id, user_name, user_surname FROM users INNER JOIN friend on friend_friend_id = user_id WHERE friend_user_id= $userid";
			$rGetFriends = @mysqli_query ($dbc, $qGetFriends);

			if(mysqli_num_rows($rGetFriends) > 0){		//if there are friends, display them

				while ($row = mysqli_fetch_array($rGetFriends)) {
					$friend_id = $row['user_id'];
					$friend_username = $row['user_name'] . " ". $row['user_surname'];
					echo "<table>
							<tr>";
					$path = "uploads/profile". $row['user_id'].".jpg";		
					if(file_exists($path)){ //if user has a profile image, display it, otherwise display a default profile img
						echo "<th class='home-table-image'><a href='profile.php?id=$friend_id'><img src ='uploads/profile". $row['user_id'].".jpg' class ='home-image-friends' width='60' height='60'></a></th>";
					}else{
						echo "<th class='home-table-image'><a href='profile.php?id=$friend_id'><img src ='uploads/profiledefault.jpg' class ='home-image-friends' width='60' height='60'></a></th>";
					}		
					echo "	<th class='home-table-friends-name'><a href='profile.php?id=$friend_id'>$friend_username</a></th>
						</table>";
				}
			}else{
				echo '<p>Search your friends in the search box and add them to your list. </p>';
			}
			?>
		</aside>
	</section>
	

	<!-- Right side -->
	<section class="home-right">
		<!-- ******************************************************************************************************-->
		<!-- FORM FOR POSTING FEEDS (TEXT and/or IMAGES) -->
		<!-- ******************************************************************************************************-->
		<aside class="home-post">	
			<form action="" method="POST" enctype="multipart/form-data">	
				
				<!-- Textarea for posting text -->
				<textarea name="feed" placeholder="How colorful are you today?" rows="4" cols="60"></textarea>
				
				<!-- If image is selected it will display here before it is saved -->
				<img id="feed-img" src="#" alt=""/>
				<?php echo $error; ?>						

				<button type='submit' value="Post" name='submit' class='button-post-feed'> Post </button>

				<!-- Button for attaching images -->
				<label for="uploadPost"><i class="material-icons button-attach-file">attach_file</i></label>
				<input type="file" name="uploadPost" id="uploadPost" accept="image/png, image/jpeg, image/jpg" onchange="readURL2(this);"/>	
			</form>
		</aside>
				
		<aside class="home-feeds">
		

		<!-- ******************************************************************************************************-->
		<!-- DISPLAY FEEDS (chronological order) -->
		<!-- ******************************************************************************************************-->
		<?php 
		//query to retrieve feeds					 
		$q = "SELECT f.feed_id, f.feed_user_id, f.feed_date_time, f.feed_message, f.feed_photo, 
		u.user_name, u.user_surname FROM feed f INNER JOIN users u ON f.feed_user_id = u.user_id 
		where f.feed_user_id in (SELECT friend_friend_id FROM friend where friend_user_id = '$userid') or f.feed_user_id = '$userid'
		ORDER BY feed_date_time DESC";
					
		$r = @mysqli_query ($dbc, $q);
					
		// Count the number of returned rows:
		$num = mysqli_num_rows($r);
					
		if ($num !== null && $num > 0) { // If it ran OK, display the records.
			while ($row = mysqli_fetch_array($r)) {
				
				$feedUsername = $row['user_name'] . " " . $row['user_surname'];
				$feedUserId = $row['feed_user_id'];
				$feedId = $row['feed_id'];
				$feedDate = $row['feed_date_time'];
				$feedText = nl2br($row['feed_message']);
				$feedImg = $row['feed_photo'];	

														
				//Get the likes from each feed
				$friendsName = getLikes($dbc, $feedId);
				$numberOfLikes = sizeof($friendsName);
				
							
				//only owner of the feed can edit/ delete the feed			
				if($feedUserId == $userid){
					//******************************************************************************************************
					//<!-- MODAL: DELETE FEED-->
					//******************************************************************************************************
					echo "<button data-toggle=\"modal\" data-target=\"#delModal$feedId\" class=\"delete-button\"><i class=\"material-icons\">delete</i></button>";

					  //-- Modal (delete feed confirmation)--
					  echo'<div class="modal fade" id="delModal'.$feedId.'" role="dialog">
					    <div class="modal-dialog">
					    
					      <div class="modal-content">
					        <div class="modal-header">
					          <button type="button" class="close" data-dismiss="modal">&times;</button>
					          <h4 class="modal-title">Are you sure you want to delete this feed?</h4>
					        </div>

					        <div class="modal-body">
					          <p>'.$feedText.'</p>';
								if(!$feedImg == "") {	//if there is an image, display it
									echo '<img src="'.$feedImg.'" class="feed-img"/>';
								}					          
					    echo '</div>
					        <div class="modal-footer">
					          <button type="button" class="delete-button"><a href="delete.php?feedid='.$feedId.'">Delete</a></button>
					          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					        </div>
					      </div>					      
					    </div>
					  </div>';
					
					//******************************************************************************************************
					//<!-- MODAL: EDIT FEED-->
					//******************************************************************************************************
					echo "<button data-toggle=\"modal\" data-target=\"#editModal$feedId\" class=\"edit-button\"><i class=\"material-icons\">edit</i></button>";

					//-- Modal (edit feed text), image can't be changed --
					echo'<div class="modal fade" id="editModal'.$feedId.'" role="dialog">
					    <div class="modal-dialog">
					    
					      <div class="modal-content">
					        <div class="modal-header">
					          <button type="button" class="close" data-dismiss="modal">&times;</button>
					          <h4 class="modal-title">Edit</h4>
					        </div>

					        <div class="modal-body">							
							<form action="home.php" method="POST">	
								<input type="text" name="feed_id_update" value="'.$feedId.'" style="display: none">
								<textarea name="feed_update">'.$row['feed_message'].'</textarea>
								<img id="feed-img" src="'.$feedImg.'" class="feed-img"/>
								<button type="submit" name="submit" class="button-post-feed">Edit</button>
							</form>
							</div>							
							
					        <div class="modal-footer">					          
					          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					        </div>
					      </div>					      
					    </div>
					  </div>';
				}	

				/**********************************************************************************************************/
				/*********** DISPLAY all the user's and friend's feeds (order by most recent posted)*************/	
				/**********************************************************************************************************/
				echo "<div id ='display-feeds'>
				
				<div id ='feed-header'>";
					$path = "uploads/profile". $feedUserId.".jpg";		
					if(file_exists($path)){ //if user has a profile image, display it, otherwise display a default profile img
						echo "<img src ='uploads/profile".$feedUserId.".jpg' class ='feed-image-user' width='70' height='70'>";
					}else{
						echo "<img src ='uploads/profiledefault.jpg' class ='feed-image-user' width='70' height='70'>";
					}
					echo "<h2 id='feed-username'>".$feedUsername."</h2>
					<p class=\"feed-date\">$feedDate</p>
				</div>
				
				<p class=\"feed-post\">$feedText</p>";
				if(!$feedImg == ""){
					echo "<img src=\"$feedImg\" alt=\"Image Post\" class=\"feed-img\"/>";
				}				
				echo "
				<div id='feed-footer'>
					<button class=\"myBtn like\" value=\"$feedId\"><i class=\"material-icons\">favorite_border</i></button>
					<!-- Get the number of likes & list friends that liked it-->
					<span class=\"feed-list-friends\" title=\"";
						foreach($friendsName as $k => $v){
							echo $v . "\n";
						}
						echo "\"> $numberOfLikes </span></td>";

						
					/**********************************************************************************************************
					/*********** SET COMMENT *************	
					/**********************************************************************************************************/	
					echo "<button class=\"myBtn commentbtn\"><i class=\"material-icons\">comment</i></button>";
					
					/*********** This div will toggle when button 'comment' is clicked--> ****************/
				echo "<div id='createcomment' class='comment' data-theme='a'>
						<form action='' method='POST'>
						<div class='content'>
							<input type='hidden' name='fid' value='".$feedId."'>
							<input type='hidden' name='uid' value='".$userid."'>
							<textarea name='message' placeholder='Write your comment...'></textarea> <br>
							<button type='submit' name='commentSubmit' class='button-comment'> Post </button>
						</div>
						</form>
					</div>
				</div>";
				
				/**********************************************************************************************************
				/*********** GET COMMENTS FROM EACH FEED *************	
				/**********************************************************************************************************/
				$qGetComments = "SELECT * FROM comments WHERE comment_feed_id = $feedId";
				$rGetComments = @mysqli_query ($dbc, $qGetComments);					
				
				while($row2 = mysqli_fetch_assoc($rGetComments)){	//results for comments
					$commentId = $row2['comment_id'];
					
					//Get details from the user that set the comment
					$qGetUserDetails = "SELECT user_id, user_name, user_surname FROM users WHERE user_id ='".$row2['comment_user_id']."'";
					$rGetUserDetails = @mysqli_query ($dbc, $qGetUserDetails);
					
					if ($row3 = mysqli_fetch_assoc($rGetUserDetails)) {			

						//Author of the comment can delete/edit the comment
						//Author of the feed can delete any comments
						if($row3['user_id'] == $userid || $feedUserId  == $userid){							
							/**********************************************************************************************************
							/*********** MODAL DELETE COMMENT *************	
							/**********************************************************************************************************/
							echo "<button data-toggle=\"modal\" data-target=\"#delModal$commentId\" class=\"delete-button\"><i class=\"material-icons\">delete</i></button>";							
							
							 //-- Modal (delete comment confirmation)--
							  echo'<div class="modal fade" id="delModal'.$commentId.'" role="dialog">
								<div class="modal-dialog">								
								  <div class="modal-content">
									<div class="modal-header">
									  <button type="button" class="close" data-dismiss="modal">&times;</button>
									  <h4 class="modal-title">Are you sure you want to delete this comment?</h4>
									</div>
									<div class="modal-body">
									  <p>'.$row2['comment_comment'].'</p>					          
									</div>
									<div class="modal-footer">
									  <button type="button" class="delete-button"><a href="delete.php?commentid='.$commentId.'">Delete</a></button>
									  <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
									</div>
								  </div>					      
								</div>
							  </div>';
						}
						
						//Edit comment (only for author of the comment)
						if($row3['user_id'] == $userid){
							/**********************************************************************************************************
							/*********** MODAL EDIT COMMENT *************	
							/**********************************************************************************************************/
							echo "<button data-toggle=\"modal\" data-target=\"#editModal$commentId\" class=\"edit-button\"><i class=\"material-icons\">edit</i></button>";
							//-- Modal (edit feed text), image can't be changed --
							echo'<div class="modal fade" id="editModal'.$commentId.'" role="dialog">
								<div class="modal-dialog">									
									<div class="modal-content">
										<div class="modal-header">
										  <button type="button" class="close" data-dismiss="modal">&times;</button>
										  <h4 class="modal-title">Edit your comment</h4>
										</div>
										<div class="modal-body">							
										<form action="home.php" method="POST">	
											<input type="text" name="comment_id_update" value="'.$commentId.'" style="display: none">
											<textarea name="comment_update">'.$row2['comment_comment'].'</textarea>
											<button type="submit" name="submit" class="button-post-feed"> Edit</button>
										</form>
										</div>
										<div class="modal-footer">					          
										  <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
										</div>
									</div>					      
								</div>
							</div>';
						}
						
						/**********************************************************************************************************
						/*********** DISPLAY ALL COMMENTS *************	
						/**********************************************************************************************************/
						echo "<div class='container'>";
						$path = "uploads/profile". $row3['user_id'].".jpg";		
						if(file_exists($path)){ //if user has a profile image, display it, otherwise display a default profile img
							echo "<img src ='uploads/profile".$row3['user_id'].".jpg' class ='comment-image-user' width='40' height='40'>";
						}else{
							echo "<img src ='uploads/profiledefault.jpg' class ='comment-image-user' width='40' height='40'>";
						}
						echo "<span class='comment-username'>". $row3['user_name']." ". $row3['user_surname']."</span>"; 
						echo "<span class='comment-date'>" .$row2['comment_date']."</span>";
						echo "<p class='comment-message'>" . nl2br($row2['comment_comment'])."</p>";
						echo "</div>";
					}
				}				
				echo "</div>";
			}	
		}else { // If no records were returned.
			$error = "There are currently no feeds to be displayed.";
		}
	?>
	</aside>			
	</section>
</div>

<!-- ******************************************************************************************************-->
<!-- JAVASCRIPT - SETTINGS -->
<!-- ******************************************************************************************************-->
<script> 

	$(document).ready(function(){
		
		/************** Code to save likes **************/
		$('.like').click(function(){				//when 'like' button is clicked			
			var clickBtnValue =  $(this).val();; 	//get the feed id
			var ajaxurl = 'home.php',
			data =  {'feedId': clickBtnValue};

			$.post(ajaxurl, data, function (response) {            	
           	 	//Code to refresh page after user likes a feed
				//NOTE: This code uses cache to prevent the 'blinking' refresh page
           	 	function startRefresh() {
    				$.get('', function(data) {
       				 	$(document.body).html(data);    
   					 });
				}
				$(function() {
   					setTimeout(startRefresh,1000);
				});
        	});
		});
		
		/*************** Code to post a comment **************/
		$(function(){	//toggle textarea for comment
			$(".commentbtn").on("click", function(e) {	//when comment button is clicked
				e.preventDefault(); 
				$(this).siblings("div.comment").toggle();
			});
		});	
	});
	
	/********** Display image after selecting img (preview) **********/	
	/*Function to display preview of the selected image for profile picture*/
	function readURL(input) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();

			reader.onload = function (e) {
				$('#blah')
					.attr('src', e.target.result)
					.width(200)
					.height(250);
			};

			reader.readAsDataURL(input.files[0]);
		}
	}

	/*Function to display preview of the selected image for feeds*/
	function readURL2(input) {		
		if (input.files && input.files[0]) {			
				
			var reader = new FileReader();			

			reader.onload = function (e) {
				$('#feed-img')
					.attr('src', e.target.result)
					.width(480);
			};
			reader.readAsDataURL(input.files[0]);
		}
	}	

	/*Function to display Modal for editing profile image*/
	document.getElementById('button').addEventListener("click", function() {
		document.querySelector('.bg-modal').style.display = "flex";
	});

	document.querySelector('.close').addEventListener("click", function() {
		document.querySelector('.bg-modal').style.display = "none";
	});
</script>
</body>	
</html>	