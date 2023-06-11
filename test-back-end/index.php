<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Max-Age: 86400');    // cache for 1 day

/* This code is creating a PHP script that handles requests to different endpoints based on the first
segment of the URL path. */
$_SESSION["request"] = explode("/",$_SERVER["REQUEST_URI"]);
$request = $_SESSION["request"];
switch($request[1]) {
        case "user" :
            require(__DIR__ . "/user.php");
            break;
        case "calls" :
            require __DIR__ . "/calls.php";
            break;
        case "date" : 
            require __DIR__ . "/dates.php";
            break;    
        default:
            break;
    }

?>