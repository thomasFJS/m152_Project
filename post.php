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
$comment = filter_input(INPUT_POST, "comment", FILTER_SANITIZE_STRING);

if ($comment){
    StoreUsersMedia($comment);
    //header("Location: index.php");
    //exit;
  }
  else {
    $_SESSION["error"]["erreurComment"] = "Veuillez insérer un commentaire dans le champ \"Commentaire\"";

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

<div class="col s12 m8 offset-m2 l6 offset-l3">
        <div class="card-panel grey lighten-5 z-depth-1">
  <div class="row">
      <form class="col s12" method="POST" action="post.php" enctype='multipart/form-data'>   
        <div class="row">
        <div class="col s2">
              <img src="img/pdp.jpg" alt="" class="circle responsive-img" height="100px" width="100px">
            </div>
      </div>  
      <div class="row">
          <div class="input-field col s10">
            <textarea id="textarea" name="comment" class="materialize-textarea" data-length="140"></textarea>
            <label for="textarea">Comment</label>
          </div>
     </div>
     <div class="row">
     <div class="file-field col input-field s12">
      <div class="btn">
      <i class="material-icons">image</i>
        <input type="file" name="fileUploaded[]" id="fileUploaded" accept="image/*" multiple>
      </div>
      <div class="file-path-wrapper" style="display:none;">
        <input class="file-path validate input-name" type="text" placeholder="Add one or more image(s)">
      </div>
          
        </div>
     </div>
     <div class="row">
      <div class="input-field col s10">  
      <button class="btn waves-effect waves-light" type="submit" name="submit">Submit
        <i class="material-icons right">send</i>
      </button>
     </div>
     </div>
    </div> 
      </form>
    </div>
    </div>
</div>
</div>


<script type="text/javascript" src="script/materialize.min.js"></script>
<script type="text/javascript" src="script/script.js"></script>
</body>
</html>