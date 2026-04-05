<?php
namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class SubjectController{

    private $mysqli;

    
    public function __construct($mysqli)
    {
        $this->mysqli = $mysqli;
    }

    public function getSubjects(Request $request,Response $response,$args){
        $idAccount = (int)$args["id"];
        $stmt = $this->mysqli->prepare("SELECT s.* FROM subjects s JOIN users u ON s.id_user = u.id");
        $stmt->execute();
        $result = $stmt->get_result();
        $subjects = $result->fetch_assoc();

        if($subjects === NULL){
            $response->getBody()->write(json_encode(['error' => 'Subjects not found']));
            return $response->withHeader("Content-Type","application/json")->withStatus(404);
        }

        $response->getBody()->write(json_encode($subjects));
        return $response->withHeader("Content-Type","application/json")->withStatus(200);
    }

    public function getSubject(Request $request,Response $response,$args){
        $idSubject = (int)$args["idS"];
        
        $stmt = $this->mysqli->prepare("SELECT * FROM subjects WHERE id = ?");
        $stmt->bind_param("i",$idSubject);
        $stmt->execute();
        $result = $stmt->get_result();
        $subjects = $result->fetch_assoc();

        if($subjects === NULL){
            $response->getBody()->write(json_encode(['error' => 'Subjects not found']));
            return $response->withHeader("Content-Type","application/json")->withStatus(404);
        }

        $response->getBody()->write(json_encode(
            ["success: Subject succesfully find"],
            $subjects
            ));
        return $response->withHeader("Content-Type","application/json")->withStatus(200);
    }

    public function create(Request $request,Response $response,$args){
        $idAccount = (int)$args["id"];
        $data = $request->getParsedBody();
        if(!isset($data["name"])){
            $response->getBody()->write(json_encode(['error' => 'data not setted']));
            return $response->withHeader("Content-Type","application/json")->withStatus(400);
        }

        $name = $data["name"];

        $stmt = $this->mysqli->prepare("INSERT INTO subjects(name,id_user) VALUES (?,?)");
        $stmt->bind_param("si",$name,$idAccount);
        
        $idSubject = $this->mysqli->insert_id;

        if($stmt->execute()){
            $response->getBody()->write(json_encode([
                'succes' => 'Subject succesfully created',
                'id' => $idSubject,
                'name' => $name
                ]));
            return $response->withHeader("Content-Type","application/json")->withStatus(200);
        } else {
            $response->getBody()->write(json_encode(['error' => 'Creation failed']));
            return $response->withHeader("Content-Type","application/json")->withStatus(500);
        }
    }

    public function delete(Request $request,Response $response,$args){
        $idAccount = (int)$args["id"];
        $idSubject = (int)$args["idS"];

        $stmt = $this->mysqli->prepare("SELECT * FROM subjects  WHERE id = ?");
        $stmt->bind_param("i",$idSubject);
        $stmt->execute();
        $result = $stmt->get_result();
        $subjects = $result->fetch_assoc();

        if($subjects === NULL){
            $response->getBody()->write(json_encode(['error' => 'Subjects not found']));
            return $response->withHeader("Content-Type","application/json")->withStatus(404);
        }

        $stmt = $this->mysqli->prepare("DELETE FROM subjects WHERE id = ?");

        $stmt->bind_param("i",$idSubject);
        if($stmt->execute()){
            $response->getBody()->write(json_encode(['succes' => 'Subject succesfully deleted']));
            return $response->withHeader("Content-Type","application/json")->withStatus(200);
        } else {
            $response->getBody()->write(json_encode(['error' => 'Cancellation failed']));
            return $response->withHeader("Content-Type","application/json")->withStatus(500);
        }
    } 

    public function update(Request $request,Response $response,$args){
        $idAccount = (int)$args["id"];
        $idSubject = (int)$args["idS"];

        $stmt = $this->mysqli->prepare("SELECT * FROM subjects  WHERE id_user = ? AND id = ?");
        $stmt->bind_param("ii",$idAccount,$idSubject);
        $stmt->execute();
        $result = $stmt->get_result();
        $subjects = $result->fetch_assoc();

        if($subjects === NULL){
            $response->getBody()->write(json_encode(['error' => 'Subjects not found']));
            return $response->withHeader("Content-Type","application/json")->withStatus(404);
        }

        $data = $request->getParsedBody();

        if($subjects["name"] === $data["name"]){
            $response->getBody()->write(json_encode(['error' => 'Name already in use']));
            return $response->withHeader("Content-Type","application/json")->withStatus(400);
        }

        $fields = [];
        $params = [];
        $types = "";

        if(isset($data["name"])){
            $fields[] = "name = ?";
            $params[] = $data["name"];
            $types .= "s";
        }

        $params[] = $idSubject;
        $types .= "i";

        if(empty($fields)){
            $response->getBody()->write(json_encode(['error' => 'Any fields to update']));
            return $response->withHeader("Content-Type","application/json")->withStatus(400);
        }

        $stmt = $this->mysqli->prepare("UPDATE subjects SET " . implode(", ", $fields) . " WHERE id = ? ");
        $stmt->bind_param($types,...$params);

        if($stmt->execute()){
            $response->getBody()->write(json_encode([
                'succes' => 'Subject succesfully updated',
                'id' => $idSubject,
                'name' => $data["name"]
                ]));
            return $response->withHeader("Content-Type","application/json")->withStatus(200);
        } else{
            $response->getBody()->write(json_encode(['error' => 'Updated failed']));
            return $response->withHeader("Content-Type","application/json")->withStatus(500);
        }
        
    }

/*     private function exist(int $id_account,int $idSubject, Response $response){

        $stmt = $this->mysqli->prepare("SELECT * FROM subjects  WHERE id_user = ? AND id = ?");
        $stmt->bind_param("ii",$id_account,$idSubject);
        $stmt->execute();
        $result = $stmt->get_result();
        $subjects = $result->fetch_assoc();

        if($subjects === NULL){
            $response->getBody()->write(json_encode(['error' => 'Subjects not found']));
            return $response->withHeader("Content-Type","application/json")->withStatus(404);
        }
    } */

}