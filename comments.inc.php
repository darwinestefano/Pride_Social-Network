<?php
#set comments for form
function setComments($dbc){
	if (isset($_POST['commentSubmit'])) {
		$uid = $_POST['uid'];
		$date = $_POST['date'];
		$message = $_POST['message'];

		$sql = "INSERT INTO comments (comment_user_id, comment_date, comment_comment)
		 VALUES ('$uid','$date', '$message');";
		$result = @mysqli_query ($dbc, $sql);
	}
}
#get all comments from form
function getComments($dbc){
	
	$sql = "SELECT * FROM comments;";
	$result = @mysqli_query ($dbc, $sql);

	while($row = mysqli_fetch_assoc($result)){
		$id = $row['comment_user_id'];
		$sql2 = "SELECT * FROM users WHERE user_id ='$id';";
		$result2 = @mysqli_query ($dbc, $sql2);
		if ($row2 = mysqli_fetch_assoc($result2)) {
		echo "<div class='container'> <p>";
			echo "<img src ='uploads/profile".$id.".jpg' class ='img-post-testimonial'>";
			echo "<p class ='comment-name'>".$row2['user_name']."<br>"; //
		
			echo "<p class ='comment-time'>".$row['comment_date']."</p><br>";
			echo "<p class ='comment-message'>".nl2br($row['comment_comment'])."</p><br>"; #nl2br() break through lines add later DO NOT FORGET!!!!!
		#Edit page on form -->

		echo "</p>";

		if (isset($_SESSION['user_id'])) {
				if ($_SESSION['user_id'] == $row2['user_id']) {
					echo "
						<form class='delete-form' method='POST' action='".deleteComments($dbc)."'>
							<input type='hidden' name='cid' value='".$row['comment_id']."'>
							<button type='submit' name='commentDelete'><i class='material-icons'>delete</i></button>
						</form>
						<form class='edit-form' method='POST' action='editComments.php'>
							<input type='hidden' name='cid' value='".$row['comment_id']."'>
							<input type='hidden' name='uid' value='".$row['comment_user_id']."'>
							<input type='hidden' name='date' value='".$row['comment_date']."'>
							<input type='hidden' name='message' value='".$row['comment_comment']."'>
							<button><i class='material-icons'>edit</i></button>
						</form>
						";
				}else{
					echo "
						<form class='edit-form' method='POST' action='editComments.php'>
							<input type='hidden' name='date' value='".$row['comment_date']."'>
							<input type='hidden' name='message' value='".$row['comment_comment']."'>
							<button type='submit' name='commentDelete'>Replay</button>
						</form>	";
				}
			}else {
				echo "<p class='comentmessage'>You need to be login to reply</p>";
			}
			
			echo"</div>";		}
	}
}
#edit comments from form
function editComments($dbc){
	if (isset($_POST['commentSubmit'])) {
		$cid = $_POST['comment_id'];
		$uid = $_POST['comment_user_id'];
		$date = $_POST['comment_date'];
		$message = $_POST['message'];

		$sql = " UPDATE comments SET comment_comment='$message' 
		WHERE cid ='$cid';";
		$result = @mysqli_query ($dbc, $sql);
	}
}

#delete comments from form
function deleteComments($dbc){
		if (isset($_POST['commentDelete'])) {
		$cid = $_POST['cid'];
		$sql = "DELETE FROM comments WHERE comment_id = '$cid';";
		$result = @mysqli_query ($dbc, $sql);
	}
}
