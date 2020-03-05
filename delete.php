<?php
require_once $_SERVER['DOCUMENT_ROOT']. '/M152/M152_Project/inc/function.php';
$idPost = filter_input(INPUT_GET, "idPost", FILTER_VALIDATE_INT);
deletePost($idPost);
header('Location: index.php');
?>