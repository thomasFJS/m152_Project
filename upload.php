<?php
/*
*     Author              :  Fujise Thomas.
*     Project             :  m152.
*     Page                :  Upload.
*     Brief               :  Upload multiple image from forms.
*     Starting Date       :  23.01.2020.
*/
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$comment = FILTER_INPUT(INPUT_POST,"comment",FILTER_SANITIZE_STRING);
$target_dir = "./img/";
$errors = [];

if(isset($_POST["submit"])) {

    for($i = 0;$i<count($_FILES["imgUpload"]["name"]);$i++){
        $target_file = $target_dir . basename($_FILES["imgUpload"]["name"][$i]);
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        //Check size of the image
        if ($_FILES["imgUpload"]["size"][$i] > 3000000) {
            echo "Sorry, your file is too big.";
            $errors["size"] = ".";
        }
        if (file_exists($target_file)) {
            echo "Sorry, file already exists.";
            $errors["exists"] = ".";
        }  
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" 
        && $imageFileType != "gif" ) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $errors["formats"] = ".";
        }     
        //if no error, try to upload image
        if (count($errors) == 0) {        
            if (move_uploaded_file($_FILES["imgUpload"]["tmp_name"][$i], $target_file)) {
                echo "The file ". basename( $_FILES["imgUpload"]["name"][$i]). " has been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        // if everything is ok, try to upload file
        } else {
            echo "Sorry, your file was not uploaded.";
        }
    }
}
?>