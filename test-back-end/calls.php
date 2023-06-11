<?php
header('Content-type: text/javascript');
header('Content-Type: application/json');
require_once("class/dbHandler.php");
require_once("./header.php");
$DB = new DBHandler();
$request_error = "You've got a wrong id or research";
$request_URI = $_SESSION["request"];
$request_method = $_SERVER["REQUEST_METHOD"];
switch($request_method){
    case "POST":
        $encoded = file_get_contents("php://input");
        $decode = json_decode($encoded, true);
        // if they are only one argument in the URL
        if(count($request_URI)==2){
            $arrayJSON = $decode["arrayJSON"];
            foreach($arrayJSON as $index=>$slice){
                $tmpDate = explode(" ",$slice["date"]);
                $formatReplace = str_replace("\\","",$tmpDate[0]);
                $formatReplace = explode("/",$formatReplace);
                $arrayJSON[$index]["date"] = implode("-",(array_reverse($formatReplace))) . " " . $tmpDate[1];
                if($arrayJSON[$index]["duration"]=="")$arrayJSON[$index]["duration"] = "00:00:00";
            }
            array_pop($arrayJSON);
            foreach($arrayJSON as $slice){
                $DB->insert($slice,"call");
            }
            echo(true);
        }
        break;
    case "GET":
        $f = fopen("test.txt","w");
            if(count($request_URI)>2){
                switch($request_URI[2]){
                    case "week":
                        $firstDate =  $request_URI[3];
                        $secondDate =  $request_URI[4];
    
                        // $stringFirstDate = $firstDate->format('Y-m-d');
                        // $stringSecondDate = $secondDate->format('Y-m-d');

                        $dataFirstRange =  $DB->getBetween("*","call","date", $firstDate,$secondDate);
                        
                        $stringFirstDate = date_create($firstDate)->modify("-7 day")->format('Y-m-d') ;
                        $stringSecondDate = date_create($secondDate)->modify("-7 day")->format('Y-m-d');
    
                        $dataSecondRange = $DB->getBetween("*","call","date", $stringFirstDate,$stringSecondDate);
                        $dataToSend = [$dataFirstRange,$dataSecondRange];
                        echo(json_encode($dataSecondRange));
                        break;
                    case "day":
                        error_reporting(E_ALL);
                        ini_set('display_error', 1);
                        $firstDate = $request_URI[3];
                        $secondDate = $request_URI[4];
                        $firstDateData = $DB->getInDB("*","call","DATE(date)",$firstDate);
                        $secondDateData = $DB->getInDB("*","call","DATE(date)",$secondDate);
                        $dataToSend = [];
                        array_push($dataToSend,$firstDateData);
                        array_push($dataToSend,$secondDateData);
                        echo(json_encode($dataToSend));

                        break;
                    default:
                        echo($request_error);
                        break;
                }
                break;
            }
        break;
}


?>