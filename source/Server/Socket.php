<?php

namespace Server;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Server\Room;
use Chess\Game;


class Socket implements MessageComponentInterface {

    const CMD_CREATE_SINGLE = 'CREATE_SINGLE_GAME';
    const CMD_MOVE = 'MOVE_FIGURE';

    private $rooms = [];
    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
    }

    public function onMessage(ConnectionInterface $conn, $msg) {

        try {

            $msg = explode(':', $msg);

            switch ($msg[0]) {
                case static::CMD_CREATE_SINGLE:
                {
                    $room = new Room();
                    $room->addClient($conn);
                    $this->rooms[] = $room;

                    $game = $room->game();
                    $game->init($msg[1]);
                    try {
                        $game->addPlayer('no_name_1', $msg[1])->createFigures();
                        $game->addPlayer('no_name_2')->createFigures();
                        $game->start();
                    } catch (\Exception $e) {

                    }
                    $data = [
                        'action' => static::CMD_CREATE_SINGLE,
                        'board' => $game->getBoard(),
                        'players' => $game->getFigures()
                    ];

                    $response = json_encode($data, JSON_UNESCAPED_UNICODE);

                    foreach ($room->getClients() as $client) {
                        $client->send($response);
                    }
                    echo "game created\n";
                    break;
                }
                case static::CMD_MOVE:
                {
                    $room = $this->getRoom($conn);
                    /** @var Game $game */
                    $game = $room->game();
                    $figures = $game->action($msg[1], $msg[2], $msg[3]);
                    $data = [
                        'action' => static::CMD_MOVE,
                        'active' => $game->getActive(),
                        'figures' => $figures
                    ];
                    $response = json_encode($data, JSON_UNESCAPED_UNICODE);
                    foreach ($room->getClients() as $client) {
                        /** @var ConnectionInterface $client */
                        $client->send($response);
                    }
                    print_r($response);
                    echo "\n";
                    break;
                }
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
       /* foreach ($this->clients as $client) {
            if ($from !== $client) {
                // The sender is not the receiver, send to each client connected
                $client->send($msg);
            }
        }*/
    }


    protected function getRoom(ConnectionInterface $conn) {
        foreach($this->rooms as $room) {
            /** @var Room*/
            foreach($room->getClients() as $client) {
                if($conn->resourceId == $client->resourceId) {
                    return $room;
                }
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}
