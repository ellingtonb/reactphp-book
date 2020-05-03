<?php

require __DIR__ . '/vendor/autoload.php';

use React\Http\Server;
use React\Http\Response;
use Psr\Http\Message\ServerRequestInterface;

$loop = React\EventLoop\Factory::create();

$router = new Router($loop);
$router->load(__DIR__ . '/routes.php');

$server = new Server(
    function (ServerRequestInterface $request) use ($router) {
        return $router($request);
    }
);

$socket = new React\Socket\Server( 8080 , $loop );

$server->listen( $socket );

echo 'Listening on ' . str_replace ( 'tcp:' , 'http:' , $socket->getAddress()) . " \n ";

$loop->run();
