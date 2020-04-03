<?php
if (session_status() == PHP_SESSION_NONE){    
    session_start(); 
}

    if(isset($_POST['submit'])){
       $user_id = $_SESSION['user_id'];
        //Set default name for a file to generate an unique file
        $newFileName = $_POST['filename'];
        if(empty($newFileName)){
            $newFileName ="gallery"; //set file as gallary in case unfilled form
        }else{
            $newFileName = strtolower(str_replace(" ", "-",$newFileName)); 
        }

        $imageTitle = $_POST['filetitle'];
        $imageDesc = $_POST['filedesc'];

        //Get the actually image 
        $file = $_FILES['file'];

        //Get variables from image , like name, type, temp name, error and size
        $fileName = $file["name"];
        $fileType = $file["type"];
        $fileTempName = $file["tmp_name"];
        $fileError = $file["error"];
        $fileSize = $file["size"];

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
                    $fileDestination = 'img/gallery/'.$imageFullName;
                
                    //Connect to the db 
                    include_once "connect.php";
                    //Check if image is not empty
                    if(empty($imageTitle) ||empty($imageDesc)){
                        header ("Location: profile.php?upload=empty");
                        exit();
                    } else{
                        $sql = "SELECT * FROM album;";
                        $stmt = mysqli_stmt_init($dbc);
                        if (!mysqli_stmt_prepare($stmt,$sql)){
                            echo "SQL statement failed";
                        }else{
                            mysqli_stmt_execute($stmt);
                            $result =mysqli_stmt_get_result($stmt);
                            $rowCount = mysqli_num_rows($result);
                            $setImageOrder = $rowCount+1;
                            
                            $sql ="INSERT INTO album (album_user_id,album_title_album, album_image_full_name, album_order_album)
                            VALUES (?,?,?,?);";
                            if (!mysqli_stmt_prepare($stmt,$sql)){
                                echo "SQL statement failed";
                            }else{
                                mysqli_stmt_bind_param($stmt,"ssss",$user_id,$imageTitle,$imageFullName,$setImageOrder);
                                mysqli_stmt_execute($stmt);
                                move_uploaded_file($_FILES["file"]["tmp_name"], $fileDestination);
                                header("Location: profile.php?upload=success");
                            }
                        }
                    }
                }else{
                    echo "File exceeded limit size !";   
                }
            }else{
                echo "You had an error!"; 
            }
        }else{
            echo "You need to upload a proper file extension";
            exit();
        }
    }