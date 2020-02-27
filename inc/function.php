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

function StoreUsersMedia($comment)
{
    //get date of creation
    $date = date("Y-m-d H:i:s");

    $target_dir = "./uploads/";

    $sql = <<<EX
    INSERT INTO t_post (comment, creationDate) VALUES (:comment, :creationDate)
    EX;
  try {
    EDatabase::getDb()->beginTransaction();
    $req = EDatabase::getDb()->prepare($sql);
    $req->bindParam(':comment', $comment, \PDO::PARAM_STR);
    $req->bindParam(':creationDate', $date, \PDO::PARAM_STR);
    $req->execute();
    $files = $_FILES["fileUploaded"];

    $idPost = EDatabase::getDb()->LastInsertId();

    foreach ($files["error"] as $key => $error) {
      if ($error == UPLOAD_ERR_OK) {
        if (in_array($files["type"][$key], VALID_EXTENSION)) {
          $tmp_name = $files["tmp_name"][$key];
          $user_filename = basename($files["name"][$key]);
          $split = explode(".", $user_filename);
          $extension = end($split);
          $filename = uniqid() . "." . $extension;
          $filetype = $files["type"][$key];
          $target_file = $target_dir . $filename;

          move_uploaded_file($tmp_name, $target_file);
          $sql = <<<EX
          INSERT INTO t_media (mediaType, mediaName, creationDate, idPost) VALUES (:mediaType, :mediaName, :creationDate, :idPost)
          EX;
          $req = EDatabase::getDb()->prepare($sql);
          $req->bindParam(':mediaType', $filetype, \PDO::PARAM_STR);
          $req->bindParam(':mediaName', $filename, \PDO::PARAM_STR);
          $req->bindParam(':creationDate', $date, \PDO::PARAM_STR);
          $req->bindParam(':idPost', $idPost, \PDO::PARAM_INT);
          $req->execute();
        }
        else {
          throw new PDOException("Wrong type of file", 1);

        }
      }
    }
    EDatabase::getDb()->commit();
  } catch (PDOException $e) {
    echo "Can't read the database".$e->getMessage();
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
  $req->execute();
  $data = $req->fetchAll(PDO::FETCH_ASSOC);
  $posts = [];
  for ($i = 0; $i < count($data);$i++) {
    $post = [
      'idPost' => $data[$i]['idPost'],
      'comment' => $data[$i]['comment'],
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
    $message .= <<<EOT
    <div class="card">
    <div class="card-image">
    <div class="slider">
    <ul class="slides">
    EOT;

    foreach ($value['medias'] as $key => $valueMedia) {
      //La condition pour vérifier si c'est une image
      if (explode('/', $valueMedia['mediaType'])[0] == "image") {
        //$message .= "<img src='uploads/" . $valueMedia['mediaName'] . "' alt=" . $valueMedia['mediaName'] . ">";
        $isActive = ($key == 0) ? "active" : "";
        $message .= <<<EOT
        <li class="{$isActive}">
        <img src="uploads/{$valueMedia['mediaName']}" alt="{$valueMedia['mediaName']}">   
        </li>
        EOT;
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
    //Set good format for date
    $dateCreation = date_format(date_create($value["creationDate"]), 'g:ia \o\n l jS F Y');
    $message .= <<<EOT
    </ul>
    </div>
    </div>
    <div class="card-content">      
      <p>{$value["comment"]}</p>
      <p>Published : {$dateCreation}</p>
    </div>
    </div>
    EOT;
  }
return $message;
}
?>