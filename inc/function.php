<?php
/*
*     Author              :  Fujise Thomas.
*     Project             :  m152.
*     Page                :  Function.
*     Brief               :  Function page with all function needed for the website.
*     Starting Date       :  23.01.2020.
*/

function createPost(){
    $sql = "INSERT INTO t_post (comment, creationDate, modificationDate) VALUES (:comment, :creationDate, :modificationDate)";
    $req = EDatabase::getDb()->prepare($sql);
    $req->bindParam(':comment', $comment, \PDO::PARAM_STR);
    $req->bindParam(':creationDate', $creationDate, \PDO::PARAM_STR);
    $req->bindParam(':modificationDate', $modificationDate, \PDO::PARAM_STR);
    try{
        $req->execute();
    }
    catch(PDOException $e){
        echo "Can't read the database".$e->getMessage();
    }
}


?>