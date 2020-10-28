<?php

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Server\Socket;

require dirname(__DIR__) . '../../vendor/autoload.php';

$server = IoServer::factory(new HttpServer(new WsServer(new Socket())), 8080);

$server->run();
