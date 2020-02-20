<?php
/*
*     Author              :  Fujise Thomas.
*     Project             :  m152.
*     Page                :  Upload.
*     Brief               :  Upload multiple image from forms.
*     Starting Date       :  23.01.2020.
*/ 

require_once $_SERVER['DOCUMENT_ROOT'].'/M152/M152_Project/inc/dbConnect.php';
require_once $_SERVER['DOCUMENT_ROOT']. '/M152/M152_Project/inc/function.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$comment = FILTER_INPUT(INPUT_POST,"comment",FILTER_SANITIZE_STRING);
$target_dir = "./img/";
$media = [];
$errors = [];

if(isset($_POST["submit"])) {
    $total = count($_FILES['imgUpload']['name']);
    for($i = 0;$i<$total;$i++){
        $imageName = basename($_FILES["imgUpload"]["name"][$i]);
        $target_file = $target_dir . $imageName;
        $imageFileExtension = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        $imageFileType = $_FILES["imgUpload"]["type"];
        //Check size of the image
        if ($_FILES["imgUpload"]["size"][$i] > 3000000) {
            echo "Sorry, your file is too big.";
            $errors["size"] = ".";
        }
        if (file_exists($target_file)) {
            echo "Sorry, file already exists.";
            $errors["exists"] = ".";
        }  
        if($imageFileExtension != "jpg" && $imageFileExtension != "png" && $imageFileExtension != "jpeg" 
        && $imageFileExtension != "gif" ) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $errors["formats"] = ".";
        }     
        //if no error, try to upload image
        if (count($errors) == 0) {        
            if (move_uploaded_file($_FILES["imgUpload"]["tmp_name"][$i], $target_file)) {
                echo "The file ". $imageName . " has been uploaded.";   
                $media[$i] = [$imageFileType[0], $imageName, date("Y-m-d H:i:s")];            
            } else {
                echo "Sorry, there was an error uploading your file.";
            }

        // if everything is ok, try to upload file
        } else {
            echo "Your file was not uploaded.";
        }
    }
    if(!empty($media)){
        createPost($comment,date("Y-m-d H:i:s"),$media);
    }
}
?>