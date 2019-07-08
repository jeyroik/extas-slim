<?php
require __DIR__ . '/../../vendor/autoload.php';

if (is_file(__DIR__ . '/../../.env')) {
    $dotenv = new \Dotenv\Dotenv(__DIR__ . '/../../');
    $dotenv->load();
}

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use extas\components\SystemContainer as Container;
use extas\interfaces\servers\IServerRepository as IRepo;
use extas\interfaces\servers\IServer;

$app = new \Slim\App;
$app->any('/', function(Request $request, Response $response, array $args){
    $response->withHeader('Location', $_SERVER['HTTP_ORIGIN'] . '/app/index');
    return $response;
});

$app->any('/{subject}/{operation}', function (Request $request, Response $response, array $args) {

    /**
     * @var $serverRepo \extas\interfaces\servers\IServerRepository
     * @var $servers \extas\interfaces\servers\IServer[]
     */
    $serverRepo = Container::getItem(IRepo::class);
    $servers = $serverRepo->all([IServer::FIELD__TEMPLATE => 'http.base']);

    foreach ($servers as $server) {
        $response = $server->run($request, $response, $args);
    }

    return $response;
});

$app->run();
