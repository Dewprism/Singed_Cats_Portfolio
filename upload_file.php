<?php
	session_name("singedcats");
	session_start();

	require_once 'class/DB.php';

    $db = new DB();
    $db->connect();

    //Upload to the user's folder
    $target_dir = "images/".$_SESSION["singedcats_username"]."/";
    $target_file = $target_dir . basename($_FILES["file"]["name"]);
    $uploadOk = 1;
    $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

    // Check if image file is a actual image or fake image
    if(isset($_POST["submit"])) {
        $allowedMimes = array('image/gif', 'image/jpeg', 'image/jpg', 'image/png', 'image/bmp', 'image/wbmp');
        $check = getimagesize($_FILES["file"]["tmp_name"]);
        $type = strtolower($check['mime']);
        if(in_array($type, $allowedMimes)) {
        //if($check !== false) {
            //echo "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
           // echo "File is not an image.";
            $uploadOk = 0;
        }
    }
    // Check if file already exists
    if (file_exists($target_file)) {
        //echo "Sorry, file already exists.";
        $uploadOk = 0;
    }
    // Check file size
    if ($_FILES["file"]["size"] > 3000000) {
        //echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }
    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
        //echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        header("Location: upload.php?e=fail");
        //echo "Your file was not uploaded.";
    // If everything is ok, try to upload file
    } else {
        //If the upload works, update the database
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            
            //Add to art table
			$art = array("artistID"=>$_SESSION["singedcats_userID"], "filename"=>"'".$_FILES["file"]["name"]."'", "category"=>"'".$_POST["category"]."'", "description"=>"'".mysql_real_escape_string($_POST["description"])."'");
			$artID = $db->insert($art, "art");

            //Records tags
			$tagmax = $db->selectCount("id","tags");
			for ($i = 0; $i < $tagmax; $i++) {
				if (isset($_POST[$i])) {
					$tag = array("artID"=>$artID,"tagID"=>$i);
					$db->insert($tag, "art_tags");
				}
			}
            
			header("Location: upload.php?e=success");
            //echo "The file ". basename( $_FILES["file"]["name"]). " has been uploaded.";
        } else {
            header("Location: upload.php?e=fail");
            //echo "Sorry, there was an error uploading your file.";
        }
    }
?>