<?php
header('Content-type: text/javascript');
require_once("class/dbHandler.php");
require_once("class/userClass.php");
require_once("./header.php");

$DB = new DBHandler();
$request_error = "You've got a wrong id or research";
$request_URI = $_SESSION["request"];
$request_method = $_SERVER["REQUEST_METHOD"];
switch($request_method){
    case "POST":
        $encoded = file_get_contents("php://input");
        $decode = json_decode($encoded, true);
        if(count($request_URI)>2){
            if(intval($request_URI[2])==0){
                switch($request_URI[2]){
                    case "login":
                        $email = $decode["email"];
                        $password = $decode["password"];
                        $dataUser = $DB->getInDB("*","user","email",$email);
                        if(count($dataUser)<=0)echo("false");
                        else{
                            $encryptPasword = $dataUser[0]["password"];
                            if(password_verify($password,$encryptPasword)){
                                echo(json_encode($dataUser));
                            }else{
                                echo("false");
                            }
                        }
                        break;
                    case "register":
                        $name = $decode["name"];
                        $surname = $decode["surname"];
                        $email = $decode["email"];
                        $password = $decode["password"];
                        $canAdd = $decode["canAdd"];
                        $user = new User($name,$surname,$email,$password,$canAdd);
                        $user->addUser();
                        echo(true);
                        break;
                }
            }
        }
        break;
    case "GET":
        echo"GETTING" ;
        break;
}
?>