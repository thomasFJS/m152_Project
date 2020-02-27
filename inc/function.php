<?php
/*
*     Author              :  Fujise Thomas.
*     Project             :  m152.
*     Page                :  Function.
*     Brief               :  Function page with all function needed for the website.
*     Starting Date       :  23.01.2020.
*/
require_once $_SERVER['DOCUMENT_ROOT'].'/M152/M152_Project/inc/dbConnect.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/M152/M152_Project/inc/ExtentedPDO.php';
define('VALID_EXTENSION', array(
    "image/png",
    "image/jpeg",
    "video/mp4",
    "audio/mp3"
));
define('UPLOADS_DIR', realpath(dirname(__FILE__)) . './uploads/');

/**
 * Function for post creation with all the media the user upload 
 */
function createPost($comment, $creationDate, $mediaArray){
    
    //First insert
    $sql = <<<EX
    INSERT INTO t_post (comment, creationDate) VALUES (:comment, :creationDate)
    EX;  
    try{ 
        //Start first transaction
    EDatabase::getDb()->beginTransaction();
    $req = EDatabase::getDb()->prepare($sql);
    $req->bindParam(':comment', $comment, \PDO::PARAM_STR);
    $req->bindParam(':creationDate', $creationDate, \PDO::PARAM_STR);
    try{
        //Execute the insert and commit the transaction
        $req->execute();
        $postId = EDatabase::getDb()->lastInsertId();
        EDatabase::getDb()->commit();
    }
    catch(PDOException $e){
        //If execute fail
        echo "Can't read the database".$e->getMessage();
        EDatabase::getDb()->rollBack();
    }   
    //Foreach media 
    foreach($mediaArray as $media){
        //Second insert
        $sql = <<<EX
        INSERT INTO t_media (mediaType, mediaName, creationDate) VALUES (:mediaType, :mediaName, :creationDate)
        EX;
        //Start another transaction
        EDatabase::getDb()->beginTransaction();

        $req = EDatabase::getDb()->prepare($sql);
        $req->bindParam(':mediaType', $media[0], \PDO::PARAM_STR);
        $req->bindParam(':mediaName', $media[1], \PDO::PARAM_STR);
        $req->bindParam(':creationDate', $media[2], \PDO::PARAM_STR);
        try{
            $req->execute();
            $lastMediaId = EDatabase::getDb()->LastInsertId();
        }
        catch(PDOException $e){
            echo "Can't read the database".$e->getMessage();
            EDatabase::getDb()->rollBack();
        }

        //Third insert
        $sql = <<<EX
        INSERT INTO t_contenir (idMedia, idPost) VALUES (:idMedia, :idPost) 
        EX;
        $req = EDatabase::getDb()->prepare($sql);
        $req->bindParam('idMedia', $lastMediaId, \PDO::PARAM_INT);
        $req->bindParam(':idPost', $postId, \PDO::PARAM_INT);
        try{
            $req->execute();
        }
        catch(PDOException $e){
            echo "Can't read the database".$e->getMessage();
            EDatabase::getDb()->rollBack();
        }       
    } 
    EDatabase::getDb()->commit(); 
}
catch(Exception $e){
    EDatabase::getDb()->rollBack();
}
}

function getAllPost(){
    $medias = [];
    $posts = [];
    $sql = <<<EX
    SELECT p.idPost, p.comment, p.creationDate, p.modificationDate 
    FROM t_post as p
    EX;
    $req = EDatabase::getDb()->prepare($sql);
    try{
        $req->execute();
        $posts = $req->fetchAll();
    }
    catch(PDOException $e){
        echo "Can't read the database".$e->getMessage();
    }
    if($req->rowCount() > 0){
        $sql = <<<EX
        SELECT c.idPost, m.idMedia, m.mediaType, m.mediaName, m.creationDate, m.modificationDate 
        FROM t_media as m JOIN t_contenir as c ON c.idMedia = m.idMedia
        EX;
        try{
            $req->execute();
            $medias = $req->fetchAll();
        }
        catch(PDOException $e){
            echo "Can't read the database".$e->getMessage();
        }
    }
    foreach($posts as $post)
    {
        
    }

}

function StoreUsersMedia($comment)
{
    $target_dir = "./uploads/";

    $sql = <<<EX
    INSERT INTO t_post (comment, creationDate) VALUES (:comment)
    EX;
  try {
    EDatabase::getDb()->beginTransaction();
    $req = EDatabase::getDb()->prepare($sql);
    $req->bindParam(':comment', $comment, \PDO::PARAM_STR);
    $req->bindParam(':creationDate', date("Y-m-d H:i:s"), \PDO::PARAM_STR);
    $req->execute();
    $files = $_FILES["fileUploaded"];

    $idPost = EDatabase::getDb()->LastInsertId();

    foreach ($files["error"] as $key => $error) {
      if ($error == UPLOAD_ERR_OK) {
        if (in_array($files["type"], VALID_EXTENSION)) {
          $tmp_name = $files["tmp_name"][$key];
          $user_filename = basename($files["name"][$key]);
          $split = explode(".", $user_filename);
          $extension = end($split);
          $filename = uniqid() . "." . $extension;
          $filetype = $files["type"][$key];
          $target_file = $target_dir . $user_filename;

          move_uploaded_file($tmp_name, $target_file);
          $sql = <<<EX
          INSERT INTO t_media (mediaType, mediaName, creationDate, idPost) VALUES (:mediaType, :mediaName, :creationDate, :idPost)
          EX;
          $req = EDatabase::getDb()->prepare($sql);
          $req->bindParam(':mediaType', $filetype, \PDO::PARAM_STR);
          $req->bindParam(':mediaName', $filename, \PDO::PARAM_STR);
          $req->bindParam(':creationDate', date("Y-m-d H:i:s"), \PDO::PARAM_STR);
          $req->bindParam(':creationDate', $idPost, \PDO::PARAM_INT);
          $req->execute();
        }
        else {
          throw new PDOException("Mauvais type de fichier", 1);

        }
      }
    }
    EDatabase::getDb()->commit();
  } catch (PDOException $e) {

    EDatabase::getDb()->rollback();

  }
}

function ShowAllMedia()
{
    $sql = <<<EX
    SELECT p.idPost, p.comment, m.mediaType, m.mediaName, p.creationDate, p.modificationDate
    FROM t_post AS p
    JOIN t_media AS m
    ON m.idPost = p.idPost
    ORDER BY p.idPost DESC
    EX;
  $req = EDatabase::getDb()->prepare($sql);

  $data = $req->fetchAll(PDO::FETCH_ASSOC);
  $posts = [];

  for ($i = 0; $i < count($data);) {
    $post = [
      'idPost' => $data[$i]['idPost'],
      'commentaire' => $data[$i]['comment'],
      'creationDate' => $data[$i]['creationDate'],
      'modificationDate' => $data[$i]['modificationDate']
    ];

    $medias = [];

    do {
      $medias[] = [
          'mediaType' => $data[$i]['mediaType'],
          'mediaName' => $data[$i]['mediaName'],
      ];

      $i++;
    } while ($i < count($data) && $data[$i - 1]['idPost'] == $data[$i]['idPost']);

    $post['medias'] = $medias;
    $posts[] = $post;
  }
  $message = "";
  foreach ($posts as $post => $value) {

    $message .= "<p>" . $value['commentaire'] . "</p>";

    foreach ($value['medias'] as $key => $valueMedia) {
      //La condition pour vérifier si c'est une image
      if (explode('/', $valueMedia['mediaType'])[0] == "image") {
        $message .= "<img src='uploads/" . $valueMedia['mediaName'] . "' alt=" . $valueMedia['mediaName'] . ">";
      }
      //Condition pour la vidéo
      elseif (explode('/', $valueMedia['mediaType'])[0] == "video") {
        $message .= "<video autoplay loop controls>";
        $message .= "<source src='uploads/" . $valueMedia['mediaName'] . "' type='" . $valueMedia['mediaType'] . "'>";
        $message .= "Your browser does not support the video tag.";
        $message .= "</video>";
      }
      //Condition pour l'audio
      elseif (explode('/', $valueMedia['mediaType'])[0] == "audio") {
        $message .= "<audio controls>";
        $message .= "<source src='uploads/" . $valueMedia['mediaName'] . "' type='" . $valueMedia['mediaType'] . "'>";
        $message .= "Your browser does not support the audio element.";
        $message .= "</audio>";
      }
    }
    $message .= "<br>publié " . $value["datePost"];
  }
return $message;
}
?>