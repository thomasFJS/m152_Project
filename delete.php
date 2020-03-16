<?php
require_once $_SERVER['DOCUMENT_ROOT']. '/M152/M152_Project/inc/function.php';
$idPost = filter_input(INPUT_GET, "idPost", FILTER_VALIDATE_INT);
var_dump(getMediaName($idPost));
foreach(getMediaName($idPost) as $key => $value){
    unlink('./uploads/'. $value['mediaName']);
}
deletePost($idPost);
header('Location: index.php');
?>