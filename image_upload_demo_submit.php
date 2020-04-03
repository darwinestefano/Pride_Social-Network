<?php
include('./functions.php');

	/*defined settings - start*/
	ini_set("memory_limit", "99M");
	ini_set('post_max_size', '20M');
	ini_set('max_execution_time', 600);
	define('IMAGE_SMALL_DIR', './uploades/small/');
	define('IMAGE_SMALL_SIZE', 50);
	define('IMAGE_MEDIUM_DIR', './uploades/medium/');
	define('IMAGE_MEDIUM_SIZE', 250);
	
	/*defined settings - end*/
	if(isset($_FILES['image_upload_file'])){
			$output['status']=FALSE;
			set_time_limit(0);
			$allowedImageType = array("image/gif",   "image/jpeg",   "image/pjpeg",   "image/png",   "image/x-png"  );

				if ($_FILES['image_upload_file']["error"] > 0) {
				$output['error']= "Error in File";
				}
				elseif (!in_array($_FILES['image_upload_file']["type"], $allowedImageType)) {
				$output['error']= "You can only upload JPG, PNG and GIF file";
				}
				elseif (round($_FILES['image_upload_file']["size"] / 1024) > 4096) {
				$output['error']= "You can upload file size up to 4 MB";
				} else {
						/*create directory with 777 permission if not exist - start*/
						createDir(IMAGE_SMALL_DIR);
						createDir(IMAGE_MEDIUM_DIR);
						/*create directory with 777 permission if not exist - end*/
						$path[0] = $_FILES['image_upload_file']['tmp_name'];
						$file = pathinfo($_FILES['image_upload_file']['name']);
						$fileType = $file["extension"];
						$desiredExt='jpg';
						$fileNameNew = rand(333, 999) . time() . ".$desiredExt";
						$path[1] = IMAGE_MEDIUM_DIR . $fileNameNew;
						$path[2] = IMAGE_SMALL_DIR . $fileNameNew;
						
								//Connect to the db 
								include_once "connect.php";
								$sql = "SELECT * FROM users;";
								$stmt = mysqli_stmt_init($dbc);
								if (!mysqli_stmt_prepare($stmt,$sql)){
									echo "SQL statement failed";
								}else{
									mysqli_stmt_execute($stmt);
									$result =mysqli_stmt_get_result($stmt);
									$rowCount = mysqli_num_rows($result);
									$sql ="INSERT INTO users (user_image) VALUES ($fileNameNew);";
									mysqli_stmt_execute($stmt);
									$fileDestination = 'img/gallery/'.$fileNameNew;
									move_uploaded_file($fileNameNew, $fileDestination);
									header("Location: profile.php?upload=success");
									
								}

						if (createThumb($path[0], $path[1], $fileType, IMAGE_MEDIUM_SIZE, IMAGE_MEDIUM_SIZE,IMAGE_MEDIUM_SIZE)) {

							if (createThumb($path[1], $path[2],"$desiredExt", IMAGE_SMALL_SIZE, IMAGE_SMALL_SIZE,IMAGE_SMALL_SIZE)) {
								$output['status']=TRUE;
								$output['image_medium']= $path[1];
								$output['image_small']= $path[2];
							}
						}
				}
		echo json_encode($output);
	}
?>