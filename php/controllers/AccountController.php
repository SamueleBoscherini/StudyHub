<?php
namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class AccountController{

    private $mysqli;

    
    public function __construct($mysqli)
    {
        $this->mysqli = $mysqli;
    }

    public function register(Request $request,Response $response, $args){
        $data = $request->getParsedbody();

        if(!isset($data["surname"]) || !isset($data["name"]) || !isset($data["password"]) || !isset($data["nickname"])){
            $response->getBody()->write(json_encode(['error' => 'data not setted']));
            return $response->withHeader("Content-Type","application/json")->withStatus(400);
        }

        $surname = $data["surname"];
        $name = $data["name"];
        $pass = $data["password"];
        $nick = $data["nickname"];

        $stmt = $this->mysqli->prepare("SELECT * FROM users WHERE nickname = ? ");
        $stmt->bind_param("s",$nick);
        $stmt->execute();
        $result = $stmt->get_result();
        $account = $result->fetch_assoc();
        
        if($account !== NULL){
            $response->getBody()->write(json_encode(['error' => 'Nickname already used']));
            return $response->withHeader("Content-Type","application/json")->withStatus(409);
        }

        $stmt = $this->mysqli->prepare("INSERT INTO users(name,surname,password,nickname) VALUES (?,?,?,?)");
        $stmt->bind_param("ssss",$name,$surname,$pass,$nick);
        if($stmt->execute()){
            $response->getBody()->write(json_encode(['succes' => 'Account succesfully registred'],$account));
            return $response->withHeader("Content-Type","application/json")->withStatus(200);
        } else {
            $response->getBody()->write(json_encode(['error' => 'Registretion failed']));
            return $response->withHeader("Content-Type","application/json")->withStatus(500);
        }

    }

    public function login(Request $request, Response $response,$args){
        $data = $request->getParsedBody();
        if(!isset($data["password"]) || !isset($data["nickname"])){
            $response->getBody()->write(json_encode(['error' => 'data not setted']));
            return $response->withHeader("Content-Type","application/json")->withStatus(400);
        }

        $nick = $data["nickname"];
        $pass = $data["password"];
        
        $stmt = $this->mysqli->prepare("SELECT * FROM users where nickname = ? and password = ? ");
        $stmt->bind_param("ss",$nick,$pass);
        $stmt->execute();
        $result = $stmt->get_result();
        $account = $result->fetch_assoc();

        $accountId = $account["id"];

        if($account === NULL){
            $response->getBody()->write(json_encode(['error' => 'incorrect credentials']));
            return $response->withHeader("Content-Type","application/json")->withStatus(401);
        } 

        $response->getBody()->write(json_encode([
            'success' => 'Account succesfully logged',
            'id' => $accountId,
            'nickname' => $nick,
            'password' => $pass
        ]));
        return $response->withHeader("Content-Type","application/json")->withStatus(200);
    }

    public function getAccount(Request $request, Response $response, $args){
        $id = $args["id"];

        $stmt = $this->mysqli->prepare("SELECT * FROM users WHERE id = ? ");
        $stmt->bind_param("i",$id);
        $stmt->execute();
        $result = $stmt->get_result();
        $account = $result->fetch_assoc();

        if($account === NULL){
            $response->getBody()->write(json_encode(['error' => "Account doesn't exist"]));
            return $response->withHeader("Content-Type","application/json")->withStatus(404);
        }

        $response->getBody()->write(json_encode($account));
        return $response->withHeader("Content-Type", "application/json")->withStatus(200);
    }
}