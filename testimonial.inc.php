<?php
#set comments for form
function setTestimonial($dbc){
	if (isset($_POST['testimonialSubmit'])) {
		$uid = $_POST['uid'];
		$date = $_POST['date'];
		$message = $_POST['message-testemonial'];

		$sql = "INSERT INTO testimonials (testimonial_user_id, testimonial_date, testimonial_message)
		 VALUES ('$uid','$date', '$message');";
		$result = @mysqli_query ($dbc, $sql);
	}
}

#get all comments from form
function getTestimonial($dbc){
	$sql = "SELECT * FROM testimonials;";
	$result = @mysqli_query ($dbc, $sql);

	while($row = mysqli_fetch_assoc($result)){
		$id = $row['testimonial_user_id'];
		$sql2 = "SELECT * FROM users WHERE user_id ='$id';";
		$result2 = @mysqli_query ($dbc, $sql2);
		if ($row2 = mysqli_fetch_assoc($result2)) {
		echo "<div class='container'> <p>";
			echo "<img src ='uploads/profile".$id.".jpg' class ='img-post-testimonial'>";
			echo "<p class ='comment-name'>".$row2['user_name']."<br>"; //
			echo "<p class ='comment-time'>".$row['testimonial_date']."</p><br>";
			echo "<p class ='comment-message'>".nl2br($row['testimonial_message'])."</p><br>"; #nl2br() break through lines add later DO NOT FORGET!!!!!
		#Edit page on form -->

		echo "</p>";

		if (isset($_SESSION['user_id'])) {
				if ($_SESSION['user_id'] == $row2['user_id']) {
					echo "
						<form class='delete-form' method='POST' action='".deleteTestimonial($dbc)."'>
							<input type='hidden' name='cid' value='".$row['testimonial_id']."'>
							<button type='submit' name='testimonialDelete'><i class='material-icons'>delete</i></button>
						</form>
					
						";
				}
			}else {
				echo "<p class='comentmessage'>You need to be login to reply</p>";
			}
			
			echo"</div>";		}
	}
}
#edit comments from form
function editTestimonial($dbc){
	if (isset($_POST['testimonialSubmit'])) {
		$cid = $_POST['testimonial_id'];
		$uid = $_POST['testimonial_user_id'];
		$date = $_POST['testimonial_date'];
		$message = $_POST['testimonial_message'];

		$sql = " UPDATE testimonials SET testimonial_message='$message' 
		WHERE testimonial_id ='$cid';";
		$result = @mysqli_query ($dbc, $sql);
	
	}
}

#delete comments from form
function deleteTestimonial($dbc){
		if (isset($_POST['testimonialDelete'])) {
			$cid = $_POST['cid'];
			$sql = "DELETE FROM testimonials WHERE testimonial_id = '$cid';";
			$result = @mysqli_query ($dbc, $sql);
	}
}
