<?php // The user is redirected here from login.php.
if (session_status() == PHP_SESSION_NONE){    
	session_start(); 

	include_once 'connect.php'; //Connect to database 
	include 'comments.inc.php'; //Include file comment
	include 'testimonial.inc.php'; //include file testimonial
	
}
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
							<a href="messeger.php">Messager</a>
							<a href="logout.php">Log out</a>
						  </div>
					</div>
				<a href="home.php">	<img src="images/logo.png" alt="Logo" height="30" width="120"> </a>
					<form action="" method="POST">
						<input type="text" placeholder="Search Friends" name="search"> 
						<button class ="button-search" type="submit"><i class="material-icons">search</i></button><br/><br/>
					</form>
				</nav>	
		</header>
			<div class= "profile-page">
				<section>
					<aside class="aside-profile">		
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
												$sqlImg ="SELECT * FROM profileimg WHERE profileimg_user_id ='$id';";
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
						#Username config- This section print out username to screen 
						echo "<h1>" . $_SESSION['user_name']  . " " . $_SESSION['user_surname'] ."</h1>"
						?>
						<ul>
							<li><a href="home.php" class="profile-profile-link"><i class="material-icons">home</i></a></li>
							<li><a href="" class="profile-friends"><i class="material-icons">people</i></a></li>
							<li><a href="" class="profile-notification"><i class="material-icons">notifications_none</i></a></li>
						</ul>			
					</aside>
								<!-- ******************************************************************************************************-->
								<!-- USER ABOUT ME SECTION-->
								<!-- ******************************************************************************************************-->
						<article class="article-profile">
									<?php
										if (isset($_SESSION['user_id'])) {
											$uid = $_SESSION['user_id'];
												$sql  = "SELECT * FROM users where user_id = '$uid';";
												$result  = mysqli_query($dbc, $sql);
												if($row = mysqli_fetch_assoc($result)){
													echo "<p>".$row['user_description']."<p>";
												}
										} 
										if(isset($_SESSION['user_id']) == ''){
											echo "What would you like to tell the world about yourself? Here you are free to
								 					communicate whatever you want in your unique way...
								 					free of judgments or prejudice. Express yourself! Show them how wonderful you are."	;
										}

									$uid = $_SESSION['user_id'];
									$query  = "SELECT COUNT(friend_user_id) FROM friend where friend_user_id = '$uid';";
									$r  = mysqli_query($dbc, $query);
												if($row = mysqli_fetch_assoc($r)){
													$friends = $row['COUNT(friend_user_id)'];
									}

									echo "
									<table>
										 <tr>
										    <th>Friends</th>
										    <th>Followers</th> 
										    <th>Communities</th>
										  </tr>
										  <tr>
										    <td> ".$friends."</td>
										    <td>0</td> 
										    <td>0</td> 
										  </tr>
										</table>
									";
									
								?>
								</article>
							</section>
						<section>				
								<!-- ******************************************************************************************************-->
								<!-- USER TESTEMONIAL FEED-->
								<!-- ******************************************************************************************************-->
							<aside class="aside-feed">
									<h1>Testimonials</h1>
									<?php

										if (isset($_SESSION['user_id'])){
											echo"
											<div class='container-testimonial'> 
												<form method='POST' action='".setTestimonial($dbc)."'>
													<input type='hidden' name='uid' value='".$_SESSION['user_id']."'>
													<input type='hidden' name='date' value='".date('Y-m-d H:i:s')."'>
													<textarea name='message-testemonial'placeholder='Leave me a testimonial...' ></textarea>
													<button type='submit' name='testimonialSubmit' class='button-testimonial'> Post </button>
												</form>
											</div>";
										}

										echo "<div class='container-testimonial'>
												  <p>".getTestimonial($dbc)."</p>
											</div>";

									?>

							</aside>
								<!-- ******************************************************************************************************-->
								<!-- USER GALLERY-->
								<!-- ******************************************************************************************************-->
							<article class="article-album">
									<section class="gallery-links">
										<div class= "wrapper">
										<?php
										#Gallery conf-  This section get an photo to be uploaded by user - Comment is a must to post the photo 
											if(isset($_SESSION['user_id'])){
												$id = $_SESSION['user_id'];
													echo "<button id='myBtn'><i class='material-icons'>add</i></button>
															<div id='myModal' class='modal'>
																	<div class='modal-content'>
																		 <div class='close'>+</div>
																			<h1> Add new photo</h1>
																				<div class='gallery-upload'>
																					<form action= 'gallery-upload.inc.php' method ='post' enctype='multipart/form-data' class='add-new-photo'>
																						<img img src ='uploads/profiledefault.jpg' class='image' alt='your image'  id='blah2'/>
																						<input type='file' name='file' id='file-gallery' onchange='readURL2(this);'>
																						<label for='file-gallery' class='btn-3'><span>Select an image gorgeous!</span></label>
																						<input type ='hidden' name='filename' value='profile".$id."'>
																						<input type ='text' name='filetitle'  placeholder = 'Say something...'>
																						<input type ='hidden' name='filedesc' value='profile".$id."'>
																						<button type='submit' name= 'submit' id='myBtn-post-gallery'> Post </button>
																					</form>
																				</div>
																		</div>
																  </div>	
														 ";
											}
											?>

											<h2 class="gallery-title"></h2>
											<div class="gallery-container">
											<?php
											#Gallery conf-  This sections is reponsible for print out all images stored in the user db 
												if(isset($_SESSION['user_id'])){
													$userid = $_SESSION['user_id'];
													$usersurname= $_SESSION['user_surname'];
													$username = $_SESSION['user_name'];
													
													$sql ="SELECT * FROM album WHERE album_user_id = '$userid' ORDER BY album_order_album DESC;";
													$stmt = mysqli_stmt_init ($dbc);
													if (!mysqli_stmt_prepare($stmt,$sql)){
														echo "Fail";
													}else{
														mysqli_stmt_execute($stmt);
														$result = mysqli_stmt_get_result($stmt);

														while($row =mysqli_fetch_assoc($result)){
																echo "
																	<div id ='user-post'>
																			<img src ='uploads/profile".$id.".jpg' class ='img-post-gallery'>
																			<h2 id='album-comment-user'>".$username  ." ".  $usersurname."</h2>
																	</div>
																			<a href='#'>
																			<div style= 'background-image: url(img/gallery/".$row['album_image_full_name'].");'></div>
																			<h3 class='album-comment'>".$row['album_title_album']."</h3>
																  		</a>
																   ";
														}	
													}
												}
													
											?>
											</div>
									</div>
									</section>	
							</article>
					</section>
			</div>
						<!-- ******************************************************************************************************-->
						<!-- JAVASCRIPT - SETTINGS -->
						<!-- ******************************************************************************************************-->
				<!-- Display image before posting--> 
						<script type="text/javascript">
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
							function readURL2(input) {
								   if (input.files && input.files[0]) {
									var reader = new FileReader();

									reader.onload = function (e) {
										$('#blah2')
											.attr('src', e.target.result)
											.width(200)
											.height(250);
									};

									reader.readAsDataURL(input.files[0]);
								}
							}
						</script>
					
						<script>
								document.getElementById('button').addEventListener("click", function() {
									document.querySelector('.bg-modal').style.display = "flex";
								});

								document.querySelector('.close').addEventListener("click", function() {
									document.querySelector('.bg-modal').style.display = "none";
								});
						</script>
					

						<script>
								var modal = document.getElementById('myModal');
								var btn = document.getElementById("myBtn");
								var span = document.getElementsByClassName("close")[0];
								btn.onclick = function() {
								  modal.style.display = "block";
								}
								span.onclick = function() {
								  modal.style.display = "none";
								}
								window.onclick = function(event) {
								  if (event.target == modal) {
									modal.style.display = "none";
								  }
								}
						</script>

					
	</body>	
</html>	