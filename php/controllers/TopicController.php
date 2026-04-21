<?php 
    namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class TopicController{

    private $mysqli;

    
    public function __construct($mysqli)
    {
        $this->mysqli = $mysqli;
    }

    public function create(Request $request,Response $response,$args){
        $idSubject = (int)$args["idS"];
        $data = $request->getParsedBody();
        if(!isset($data["title"])){
            $response->getBody()->write(json_encode(['error' => 'data not setted']));
            return $response->withHeader("Content-Type","application/json")->withStatus(400);
        }

        $title = $data["title"];

        $stmt = $this->mysqli->prepare("INSERT INTO topics(title,id_subject) VALUES (?,?)");
        $stmt->bind_param("si",$title,$idSubject);

        $idTopic = $this->mysqli->insert_id;

        if($stmt->execute()){
            $response->getBody()->write(json_encode([
                'succes' => 'Topic succesfully created',
                'id' => $idTopic,
                'title' => $title
                ]));
            return $response->withHeader("Content-Type","application/json")->withStatus(200);
        } else {
            $response->getBody()->write(json_encode(['error' => 'Creation failed']));
            return $response->withHeader("Content-Type","application/json")->withStatus(500);
        }

        
    }

    public function getTopic(Request $request,Response $response,$args){
        $idTopic = (int)$args["idT"];
        
        $stmt = $this->mysqli->prepare("SELECT * FROM topics WHERE id = ?");
        $stmt->bind_param("i",$idTopic);
        $stmt->execute();
        $result = $stmt->get_result();
        $topics = $result->fetch_assoc();

        if($topics === NULL){
            $response->getBody()->write(json_encode(['error' => 'Topics not found']));
            return $response->withHeader("Content-Type","application/json")->withStatus(404);
        }

        $response->getBody()->write(json_encode(
            ["success: Topic succesfully find"],
            $topics
            ));
        return $response->withHeader("Content-Type","application/json")->withStatus(200);
    }

    public function getTopics(Request $request,Response $response,$args){
        $idTopic = (int)$args["idT"];

        $stmt = $this->mysqli->prepare("SELECT * FROM topics WHERE id = ?");
        $stmt->bind_param("i",$idTopic);
        $stmt->execute();
        $result = $stmt->get_result();
        $topics = $result->fetch_assoc();

        if($topics === NULL){
            $response->getBody()->write(json_encode(['error' => 'Topic not found']));
            return $response->withHeader("Content-Type","application/json")->withStatus(404);
        }

        $response->getBody()->write(json_encode($topics));
        return $response->withHeader("Content-Type","application/json")->withStatus(200);

    }

    public function delete(Request $request,Response $response,$args){
        $idTopic = (int)$args["idT"];

        $stmt = $this->mysqli->prepare("SELECT * FROM topics  WHERE id = ?");
        $stmt->bind_param("i",$idTopic);
        $stmt->execute();
        $result = $stmt->get_result();
        $topics = $result->fetch_assoc();

        if($topics === NULL){
            $response->getBody()->write(json_encode(['error' => 'Topic not found']));
            return $response->withHeader("Content-Type","application/json")->withStatus(404);
        }

        $stmt = $this->mysqli->prepare("DELETE FROM topics WHERE id = ?");

        $stmt->bind_param("i",$idTopic);
        if($stmt->execute()){
            $response->getBody()->write(json_encode(['succes' => 'Topic succesfully deleted']));
            return $response->withHeader("Content-Type","application/json")->withStatus(200);
        } else {
            $response->getBody()->write(json_encode(['error' => 'Cancellation failed']));
            return $response->withHeader("Content-Type","application/json")->withStatus(500);
        }

    }

    public function update(Request $request,Response $response,$args){
        $idSubject = (int)$args["idS"];
        $idTopic = (int)$args["idT"];

        $stmt = $this->mysqli->prepare("SELECT * FROM subjects  WHERE id_subject = ? AND id = ?");
        $stmt->bind_param("ii",$idSubject,$idTopic);
        $stmt->execute();
        $result = $stmt->get_result();
        $topic = $result->fetch_assoc();

        if($topic === NULL){
            $response->getBody()->write(json_encode(['error' => 'Topic not found']));
            return $response->withHeader("Content-Type","application/json")->withStatus(404);
        }

        $data = $request->getParsedBody();

        if($topic["title"] === $data["title"]){
            $response->getBody()->write(json_encode(['error' => 'Titolo already in use']));
            return $response->withHeader("Content-Type","application/json")->withStatus(400);
        }

        $fields = [];
        $params = [];
        $types = "";

        if(isset($data["title"])){
            $fields[] = "title = ?";
            $params[] = $data["title"];
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
                'title' => $data["title"]
                ]));
            return $response->withHeader("Content-Type","application/json")->withStatus(200);
        } else{
            $response->getBody()->write(json_encode(['error' => 'Updated failed']));
            return $response->withHeader("Content-Type","application/json")->withStatus(500);
        }
        
    }
}