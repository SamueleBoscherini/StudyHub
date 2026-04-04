<?php
use Slim\Factory\AppFactory;
require __DIR__ . '/connessione.php';

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/controllers/AccountsController.php';
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app = AppFactory::create();

$app->addBodyParsingMiddleware();

$app->get("/", function(Request $request, Response $response, $args){
    
    return $response;
});

$db = Database::getInstance()->getConnection();
$account = new AccountController($db);

    $app->post('/accounts/register', [$account, 'register']);
    $app->post('/accounts/login', [$account, 'login']);
    $app->get('/accounts/{id}', [$account, 'getAccount']);

$app->run();