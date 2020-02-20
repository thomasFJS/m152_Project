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
    $sql = "SELECT "
}
?>