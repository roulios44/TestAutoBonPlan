<?php
header('Content-type: text/javascript');
require_once("class/dbHandler.php");
require_once("./header.php");
$DB = new DBHandler();
$request_error = "You've got a wrong id or research";
$request_URI = $_SESSION["request"];
$request_method = $_SERVER["REQUEST_METHOD"];
switch($request_method){
    case "GET":
        if(count($request_URI)==2){
            echo(json_encode($DB->getAllDates()));
            break;
        }
        break;
}


?>