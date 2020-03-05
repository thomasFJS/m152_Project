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
 * Function to Store all media the users add on his post
 * 
 * @param string $comment the comment in the post
 * @return void
 */
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
/**
 * Function to get all media from database
 * 
 * @return array $posts multidimensional array with all posts we get from the database
 */
function GetAllMedia()
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
  for ($i = 0; $i < count($data);) {
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
  return $posts;
}
/**
 * Function to show all media
 * 
 * @param array $posts All posts saved in database
 * @return string $message HTML to display in home page with all posts
 */
function ShowAllMedia($posts){
  $message = "";
  foreach ($posts as $post => $value) {
    $message .= <<<EOT
    <div class="container hoverable center-align" style="width:40%;">
    <div class="card">
    <div class="card-image">
    <div class="carousel carousel-slider center-align" id="carousel" data-indicators="true">
    EOT;
 
    foreach ($value['medias'] as $key => $valueMedia) {
      //Set number of the media for carousel
      switch($key){
        case 0:
        $nbMedia = "#one!"; 
        break;
        case 1:
        $nbMedia = "#two!";
        break;
        case 2:
        $nbMedia = "#three!"; 
        break;
        case 3:
        $nbMedia = "#four!";
        break;
        case 4:
        $nbMedia = "#five!";
        break;
      }
      //Check if media is Image
      if (explode('/', $valueMedia['mediaType'])[0] == "image") {
        $message .= <<<EOT
        <div class="carousel-item center-align" href="{$nbMedia}">
        <img src="uploads/{$valueMedia['mediaName']}" alt="{$valueMedia['mediaName']}" style="width:443;height:380px;"/>   
        </div>
        EOT;
      }
      //Check if media is video
      elseif (explode('/', $valueMedia['mediaType'])[0] == "video") {
        $message .= <<<EOT
        <div class="carousel-item video-container" href="{$nbMedia}">
        <video controls autoplay loop height="400px">
        <source src="uploads/{$valueMedia['mediaName']}" type="{$valueMedia['mediaType']}">
        Your browser does not support the video tag.
        </video>
        </div>
        EOT;
      }
      //Check if media is audio
      elseif (explode('/', $valueMedia['mediaType'])[0] == "audio") {
        $message .= <<<EOT
        <div class="carousel-item audio-container center-align" href="{$nbMedia}">
        <audio controls>
        <source src="uploads/{$valueMedia['mediaName']}" type="{$valueMedia['mediaType']}">
        Your browser does not support the audio element.
        </audio>
        </div>
        EOT;
      }
    }
    //Set good format for date
    $dateCreation = date_format(date_create($value["creationDate"]), 'g:ia \o\n l jS F Y');
    $message .= <<<EOT
    </div>
    </div>
    <div class="card-content">     
      <p>{$value["comment"]}</p>
       
    </div>
    <div class="card-action">
      Published : {$dateCreation}
      <a href="#closeModal" class="right waves-effect waves-light modal-trigger"><i class="material-icons">clear</i></a>
      <a href="#editModal" class="right waves-effect waves-light modal-trigger"><i class="material-icons">edit</i></a>
      <div id="closeModal" class="modal">
      <div class="modal-content">
      <h4>Do you really want to delete this post ?</h4>
      By clicking <b>'YES'</b> this post will be permanently deleted
      </div>
      <div class="modal-footer">
        <a href="./delete.php?idPost={$value['idPost']}" class="modal-close waves-effect waves-red btn-flat">Yes</a>
        <a href="#!" class="modal-close waves-effect waves-green btn-flat">Cancel</a>
      </div>
    </div>     
    </div>
    </div>
    </div>
    EOT;
  }
return $message;
}
/**
 * Function to delete post
 * 
 * @param int $idPost id of the post you want to delete
 */
function deletePost($idPost){
  $sql = <<<EOT
  DELETE FROM t_media WHERE idPost = :idPost
  EOT;
  try{
  EDatabase::getDb()->beginTransaction();
  $req = EDatabase::getDb()->prepare($sql);
  $req->bindParam(':idPost', $idPost, PDO::PARAM_INT);
  $req->execute();
  $sql = <<<EOT
  DELETE FROM t_post WHERE idPost = :idPost
  EOT;
  $req = EDatabase::getDb()->prepare($sql);
  $req->bindParam(':idPost', $idPost, PDO::PARAM_INT);
  $req->execute();
  EDatabase::getDb()->commit();
}
catch (PDOException $e) {
  echo "Can't read the database".$e->getMessage();
  EDatabase::getDb()->rollback();
}
}
?>