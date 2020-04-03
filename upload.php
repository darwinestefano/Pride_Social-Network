<?php
session_start();

include_once 'connect.php';

$id  = $_SESSION['user_id'];
echo "id upload $id";

 if(isset($_POST['submit']))
 {
	
	$file = $_FILES['file'];

	$fileName = $file['name'];
		$fileTmpName = $file['tmp_name'];
			$fileSize = $file['size'];
				$fileError = $file['error'];
					$fileType = $file['type'];

					$fileExt = explode('.',$fileName);
					$fileActualExt =strtolower(end($fileExt));
					$allowed = array('jpg', 'jpeg', 'png');

					if(in_array($fileActualExt, $allowed)){
						if($fileError == 0){
							if($fileSize <1000000){
								$fileNameNew = "profile".$id.".".$fileActualExt;
								$fileDestination = 'uploads/'.$fileNameNew;
								move_uploaded_file($fileTmpName, $fileDestination);
								$sql = "UPDATE profileimg SET profileimg_status = 0;";
							    mysqli_query($dbc,$sql);
								header("Location:profile.php?uploadsuccess");
							}else{
								echo"Your file is too big";
							}
						}else{
							echo " There was an error uploading your file";
						}

					}else{
						echo "You cannot upload files of this type!";
					}
 }