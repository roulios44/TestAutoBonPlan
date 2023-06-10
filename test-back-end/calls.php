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
                        // $firstDate =  $request_URI[3];
                        // $secondDate =  $request_URI[4];
    
                        // // $stringFirstDate = $firstDate->format('Y-m-d');
                        // // $stringSecondDate = $secondDate->format('Y-m-d');

                        // $test = date_create($firstDate);
                        // $dataFirstRange = [];
                        // for($i=0;$i<7;$i++){
                        //     $test->modify("-1 day");
                        //     echo(json_encode($DB->getInDB("*","call","DATE(date)",$test->format('Y-m-d'))));
                        // }
                        // // $stringFirstDate = date_create($firstDate)->modify("-7 day")->format('Y-m-d') ;
                        // // $stringSecondDate = date_create($secondDate)->modify("-7 day")->format('Y-m-d');
    
                        // // $dataSecondRange = $DB->getBetween("*","call","date", $stringFirstDate,$stringSecondDate);
                        // // $dataToSend = [$dataFirstRange,$dataSecondRange];
                        // // echo(json_encode($dataFirstRange));
                        break;
                    case "day":
                        $firstDate = $request_URI[3];
                        $secondDate = $request_URI[4];
                        $firstDateData = $DB->getInDB("*","call","DATE(date)",$firstDate);
                        $secondDateData = $DB->getInDB("*","call","DATE(date)",$secondDate);
                        $dataToSend = [$firstDateData,$secondDateData];
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