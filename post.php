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
<div class="row">
      <form class="col s12">
        <div class="row">
          <div class="input-field col s6">
            <input id="input_text" type="text" data-length="10">
            <label for="input_text">Nom</label>
          </div>
        </div>
        <div class="row">
          <div class="input-field col s12">
            <textarea id="textarea2" class="materialize-textarea" data-length="120"></textarea>
            <label for="textarea2">Textarea</label>
          </div>
        </div>
        <div class="row">
        <div class="file-field input-field">
      <div class="btn">
        <span>File</span>
        <input type="file" accept="image/*" multiple>
      </div>
      <div class="file-path-wrapper">
        <input class="file-path validate" type="text" placeholder="Upload one or more files">
      </div>
     </div>
    </div> 
      </form>
    </div>

<script type="text/javascript" src="js/materialize.min.js"></script>
</body>
</html>