<?php
use Slim\Factory\AppFactory;
require __DIR__ . '/connessione.php';

require __DIR__ . '/vendor/autoload.php';
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


$app = AppFactory::create();

$app->addBodyParsingMiddleware();

$app->get("/", function(Request $request, Response $response, $args){

    return $response;
});

$db = Database::getInstance()->getConnection();

use App\Controllers\AccountController;
$account = new AccountController($db);

    $app->post('/accounts/register', [$account, 'register']);
    $app->post('/accounts/login', [$account, 'login']);
    $app->get('/accounts/{id}', [$account, 'getAccount']);


use App\Controllers\SubjectController;

$subject = new SubjectController($db);

    $app->post('/accounts/{id}/create', [$subject, 'create']);
    $app->delete('/accounts/{id}/delete/{idS}', [$subject, 'delete']);
    $app->put('/accounts/{id}/update/{idS}', [$subject, 'update']);
    $app->get('/accounts/{id}/subjects', [$subject, 'getSubjects']);
    $app->get('/accounts/{id}/subject/{idS}', [$subject, 'getSubjects']);

use App\Controllers\TopicController;

$topic = new TopicController($db);

    $app->post('/accounts/{id}/subjects/{idS}/create', [$topic, 'create']);
    $app->delete('/accounts/{id}/subjects/{idS}/celete', [$topic, 'delete']);
    $app->put('/accounts/{id}/subjects/{idS}/update', [$topic, 'update']);
    $app->get('/accounts/{id}/subjects/{idS}/topics', [$topic, 'getTopics']);
    $app->get('/accounts/{id}/subjects/{idS}/topic/{idT}', [$topic, 'getTopic']);

$app->run();