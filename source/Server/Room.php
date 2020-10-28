<?php


namespace Server;


use Chess\Game;
use Ratchet\ConnectionInterface;

class Room
{
    /** @var Game */
    protected $game = null;
    protected $clients = [];

    public function __construct()
    {
        $this->game = new Game();
    }

    /**
     * @return Game
     */
    public function game() {
        return $this->game;
    }

    public function addClient(ConnectionInterface $client)
    {
        $this->clients[] = $client;
    }

    /**
     * @return ConnectionInterface[]
     */
    public function getClients() : array
    {
        return $this->clients;
    }

}
