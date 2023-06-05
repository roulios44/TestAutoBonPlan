<?php

header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Max-Age: 86400');    // cache for 1 day

$_SESSION["request"] = explode("/",$_SERVER["REQUEST_URI"]);
$request = $_SESSION["request"];
switch($request[1]) {
        case "user" :
            require(__DIR__ . "/user.php");
            break;
        case "calls" :
            require __DIR__ . "/calls";
            break;
        default:
            break;
    }

?>