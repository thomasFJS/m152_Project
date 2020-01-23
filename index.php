<?php
/*
*     Author              :  Fujise Thomas.
*     Project             :  m152.
*     Page                :  Index.
*     Brief               :  Home page.
*     Starting Date       :  23.01.2020.
*/

require_once("./inc/dbConnect.php");
require_once("./inc/function.php");

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="FR" dir="ltr">
<head>
    <meta charset="utf-8">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="css/materialize.min.css"  media="screen,projection"/>
    <link type="text/css" rel="stylesheet" href="./css/style.css"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Accueil</title>
</head>
<body>
<?php
    include "./navbar/navbar.php";
?>
<script type="text/javascript" src="js/materialize.min.js"></script>
</body>
</html>

