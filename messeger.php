
<?php 
	session_start(); 

	include_once 'connect.php';
?>

<!DOCTYPE html>
<html lang="en">
	<head>

		<title>Profile</title>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="style.css"/>
		<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
		<script class="jsbin" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
		<script class="jsbin" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.0/jquery-ui.min.js"></script>
	</head> 
	<body>
		<header>
				<nav>
					<div class="menu-dropdown">
						<a href="" ><i class="material-icons">menu</i></a>
						  <div class="dropdown-content">
							<a href="aboutme.php">About me</a>
							<a href="#">Messager</a>
							<a href="logout.php">Log out</a>
						  </div>
					</div>
					<a href="home.php"><img src="images/logo.png" alt="Logo" height="30" width="120"></a>
					<form action="" method="POST">
						<input type="text" placeholder="Search Friends" name="search"> 
						<button class ="button-search" type="submit"><i class="material-icons">search</i></button><br/><br/>
					</form>
				</nav>	
		</header>
			<div class= "profile-page">
				<section>
						<aside class="aside-messager">		
							<!-- ******************************************************************************************************-->
								<!-- DISPLAY FRIENDS  SECTION-->
								<!-- ******************************************************************************************************-->
								<?php 
								
								$userid = $_SESSION['user_id'];
								$qGetFriends = "SELECT user_id, user_name, user_surname FROM users INNER JOIN friend on friend_friend_id = user_id WHERE friend_user_id = $userid;";

								$rGetFriends = @mysqli_query ($dbc, $qGetFriends);

								if(mysqli_num_rows($rGetFriends) > 0){		//if there are friends, display them

									while ($row = mysqli_fetch_array($rGetFriends)) {
										$friend_id = $row['user_id'];
										$friend_username = $row['user_name'] . " ". $row['user_surname'];
										echo "<table class='table-chat'>
												<tr>
												<th> </th>
												</tr>
												<tr>
												<td class='home-table-image'><a href='messeger.php?id=$friend_id'><img src ='uploads/profile". $row['user_id'].".jpg' onerror=\"this.src='uploads/profiledefault.jpg'\" class ='home-image-friends' width='60' height='60'></a></td>
												<td ><a href='messeger.php?id=$friend_id' class='home-table-friends-name'>$friend_username</a></td>
												
												</tr>							
											</table>";
									}
								}else{
									echo '<p>Search your friends in the search box and add them to your list. </p>';
								}
								?>	
									
						</aside>

						<article class="article-password">
								<?php
										//variables
										$userid = $_SESSION['user_id'];
										$username = $_SESSION['user_name'] . " " . $_SESSION['user_surname'];
										
										$qGetFriends = "SELECT user_id, user_name, user_surname FROM users INNER JOIN friend on friend_friend_id = user_id WHERE friend_user_id = $userid;";

										$rGetFriends = @mysqli_query ($dbc, $qGetFriends);

										//Get friend id from the ajax post
										if(isset($_GET['id'])){
											$friendId = $_GET['id'];
										}else{
											$friendId =null;
										}
										
										
										$_POST['friend_id'] = $friendId;
										$q = "SELECT user_name, user_surname FROM users WHERE user_id = $friendId";
										$r = @mysqli_query ($dbc, $q);
										
										if($r){		
											$num = mysqli_num_rows($r);

											if ($num > 0) { // if friend is found
											
												while ($row = mysqli_fetch_array($r)) {				
													$friend_username = $row['user_name'] . " " . $row['user_surname'];				
												}
											}
											else {
												echo "ERROR FETCHING FRIEND.";
											}
										}

										if (!$friendId == ''){

												echo "
									<div>
											<img src ='uploads/profile".$friendId.".jpg' onerror='this.src=\'uploads/profiledefault.jpg\' class ='chat-image-user' width='70' height='70'>
											<p class='chat-username'>".$friend_username."</p>
										</div>
										<div id='chatlogs'>
										
											<!-- Create a Javascript code to load the messages from our database
											The java script function which will have a xml http request instance
											it will allow us to execute a php script whithout refreshing the page 
											This function will be executed once the send link is click-->
											
											Loading chatlogs please wait....
										</div>
											<form name='form1' onsubmit='return false;' enctype='multipart/form-data' class='form-chatbox'>
													<input type='hidden' name='friend_id' value='".$friendId."'>
													<textarea name='msg'> </textarea> <br />
													<button class='btn-chatbox' onclick='submitChat()'> Send </button> <br/>
											</form>	
										";

										}else {
											echo '<p>Search your friends in the search box and add them to your list. </p>';
										}
								?>
							
								
						</article>
				</section>	
			
			</div>
					
				<script>

							  $('#form1').submit(function(event) {
								e.preventDefault();
							
						       $("#chatlogs").html('');
						       var values = $(this).serialize();
 							

								$.ajax({
									url: 'logs.php',
									type: 'GET',
									dataType: 'html',
									success: function(data) {
										$('#chatlogs').html(data); // data came back ok, so display it
									},
									error: function() {
										$('#chatlogs').prepend('Error retrieving new messages..');
									}

								});
						        
							});
							
					</script>
					<script>
								$(function(){
										$(".home-table-chat-button").click(function (e) {
											e.preventDefault();				
											var friendId =  $(this).val();; 	//get the friend id
											var ajaxurl = 'messeger.php',
											data =  {'friendId': friendId};

											$.post(ajaxurl, data, function (data) {   
												$("#chatbox").html(data);
												$("#chatbox").toggle();
											});
									});
								});

								
								function submitChat(e){
								//if one of the fields is blank exit page -gives an alert message
								if (form1.friend_id.value == '' || form1.msg.value == ''){
									alert('All Fields are mandatory');
									return;
								}

								//Store the values of the field into JavaScript variables which will be passed later to the insert.php
								var friend_id = form1.friend_id.value;
								var msg = form1.msg.value;

								//Create an http request instance 
								var xmlhttp = new XMLHttpRequest();
								//function that displays the content of insert.php - once sucessfully loaded
								//the chatlogs from the db will be displayed inside  the div section 
								// Open that and send the http request 
								xmlhttp.onreadystatechange = function(){
									if(xmlhttp.readyState==4&&xmlhttp.status==200){
										document.getElementById('chatlogs').innerHTML = xmlhttp.responseText;
									}
								}

								xmlhttp.open('GET','insert.php?uname='+friend_id+'&msg='+msg, true); //Pass two variables using query string		
								xmlhttp.send();
							}
							
	
	</script>
			
		</body>	
</html>	