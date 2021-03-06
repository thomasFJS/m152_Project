<?php
/*
*     Author              :  Fujise Thomas.
*     Project             :  m152.
*     Page                :  Index.
*     Brief               :  Home page.
*     Starting Date       :  23.01.2020.
*/

require_once $_SERVER['DOCUMENT_ROOT'].'/M152/M152_Project/inc/dbConnect.php';
require_once $_SERVER['DOCUMENT_ROOT']. '/M152/M152_Project/inc/function.php';

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
    <script type = "text/javascript" src = "https://code.jquery.com/jquery-2.1.1.min.js"></script>           
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/js/materialize.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Accueil</title>
</head>
<body>
<?php
    include "./navbar/navbar.php";
    
?>
      <div class="container center-align">
      <div class="card">
        <div class="card-image">
          <img src="./uploads/welcomeImage.jpg">
          <span class="card-title"><h1>Welcome</h1></span>
        </div>        
        <div class="card-content">
          <p>Welcome to our portfolio website.</p>
        </div>
      </div>
</div>
  <?= ShowAllMedia(GetAllMedia()); ?>  
<script type="text/javascript" src="script/materialize.min.js"></script>
<script type="text/javascript" src="./script/script.js"></script>
</body>
</html>

