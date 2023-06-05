<?php 
require_once("dbHandler.php");

class User extends DBHandler{
    private string $name;
    private string $surname;
    private string $email;
    private string $password;
    private bool $canAdd;

    function __construct(string $name,string $surname,string $email,string $password,bool $canAdd){
        parent::__construct();
        $this->email = $email;
        $this->name = $name;
        $this->surname = $surname;
        $this->password = password_hash($password,PASSWORD_BCRYPT);
        $this->canAdd = $canAdd;
    }

    function addUser(){
        $arrayData = [
            "name" => $this->name,
            "email"=> $this->email,
            "password" => $this->password,
            "surname" => $this->surname,
            "canAdd" => $this->canAdd,
        ];
        $idUSer = $this->insert($arrayData,"user");
        return $idUSer;
    }
}


?>