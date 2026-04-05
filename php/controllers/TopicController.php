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
        $idTopic = (int)$args["idS"];
        
        $stmt = $this->mysqli->prepare("SELECT * FROM topics WHERE id = ?");
        $stmt->bind_param("i",$idTopic);
        $stmt->execute();
        $result = $stmt->get_result();
        $topics = $result->fetch_assoc();
        var_dump($topics);
        exit;

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
}