<?php
/*
*     Author              :  Fujise Thomas.
*     Project             :  m152.
*     Page                :  dbConnect.
*     Brief               :  Object EDatabase (connection with the database).
*     Starting Date       :  23.01.2020.
*/
require_once $_SERVER['DOCUMENT_ROOT'].'/Project/config/config.php';
/**
 * Class EDatabase to connect the database
 */
class EDatabase {
    private static $db ;
    private function __construct(){}
    private function __clone(){}
    public static function getDb() {
        if(!self::$db){
            try{
                $connectString = DB_DBTYPE.':host='.DB_HOST.';dbname='.DB_NAME;
                    self::$db = new PDO($connectString, DB_USER, DB_PASS, array('charset'=>'utf8'));
                self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                }catch(PDOException $e){
                    echo "EDatabase Error: ".$e;
                }
        }
        return self::$db;
    }

    
}
?>