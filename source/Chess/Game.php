<?php

namespace Chess;

use Exception;
use PDO;

class Game
{
    const CHECK = 1;
    const CHECKMATE = 2;
    const PAT = 3;

    /** @var Player[] */
    protected $players = [];
    /** @var PDO */
    private $pdo = null;
    /** @var int */
    private $gameId = 0;
    /** @var bool */
    private $reverse = false;
    /** @var string */
    private $active = 'white';
    /** @var array  */
    private $output = [];

    public function __construct()
    {
        $host = '127.0.0.1';
        $db = 'chess';
        $user = 'root';
        $pass = '';
        $charset = 'utf8';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $opt = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $this->pdo = new PDO($dsn, $user, $pass, $opt);
    }

    public function getId() {
        return $this->gameId;
    }


    public function init($color = null) : void {
        $this->reverse = $color == 'black';
        $time = time();
        $this->pdo->prepare("INSERT INTO game (start_time) VALUES ($time)")->execute();
        $this->gameId = $this->pdo->lastInsertId();
    }


    /**
     * @param string $player
     * @param string|null $color
     * @return Player|string
     * @throws Exception
     */
    public function addPlayer(string $player, string $color = null) : Player
    {
        if(count($this->players) == 2) {
            throw new Exception('There can be no more than two players');
        }

        if(!$color) {
            if(isset($this->players[0])) {
                $color = $this->players[0]->get('color') == 'white' ? 'black' : 'white';
            } else {
                $color = 'white';
            }
        }

        $onTop = false;
        if(($this->reverse && $color == 'white') || (!$this->reverse && $color == 'black')) {
            $onTop = true;
        }

        $playerData = [
            'name' => $player,
            'color' => $color,
            'game_id' => $this->gameId
        ];

        $stmt = $this->pdo->prepare("INSERT INTO player (`name`, `color`, game_id) VALUES (:name, :color, :game_id)");
        $stmt->execute($playerData);
        $playerData['id'] = $this->pdo->lastInsertId();

        $player = new Player($playerData, $this, $onTop);

        $stmt = $this->pdo->prepare("INSERT INTO figure (`player_id`, `class_name`, `x`, `y`) VALUES (:playerId, :className, :x, :y)");
        foreach($player->getFigures() as $figure) {
            $stmt->execute(['playerId' => $player->get('id'), 'className' => get_class($figure), 'x' => $figure->getX(), 'y' => $figure->getY()]);
            $figure->setId($this->pdo->lastInsertId());
        }

        $this->players[] = $player;
        return $player;
    }

    public function output(int $figureId, string $action, int $x = null, int $y = null)
    {
        $this->output[] = [
            'id' => $figureId,
            'action' => $action,
            'x' => $x,
            'y' => $y
        ];
    }

    // restore session
    /*public function proceed(int $gameId) : bool {
        $gameData = $this->pdo->query("SELECT * FROM game WHERE status = 1 AND id = $gameId")->fetch();
        if($gameData) {
            $players = $this->pdo->query("SELECT * FROM player WHERE game_id = $gameData[id]")->fetchAll();
            foreach($players as $i => $player) {
                $figuresData = $this->pdo->query("SELECT * FROM figure WHERE player_id = $player[id]")->fetchAll();
                $figures = [];
                foreach ($figuresData as $figureData) {
                    $className = $figureData['class_name'];
                    $figure = new $className($figureData['x'], $figureData['y']);
                    $figure->setId($figureData['id']);
                    $figures[] = $figure;
                }
                $this->players[] = new Player($player['name'], $player['color'], $figures);
            }
            return true;
        }
        return false;
    }*/

    public function getBoard()
    {
        $isBlack = $this->reverse;
        $rows = [];
        $abc = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
        $num = ['8', '7', '6', '5', '4', '3', '2', '1'];
        $opt = ['black', 'white'];

        if($isBlack) {
            $abc = array_reverse($abc);
            $num = array_reverse($num);
            $opt = ['white', 'black'];
        }

        for($i = 0; $i <= 9; $i++) {
            if($i == 0 || $i == 9) {
                $stroke = array_merge([''], $abc, ['']);
            } else {
                $line = [];
                for($j = 0; $j < count($num); $j++) {
                    if($isBlack) {
                        if ($i % 2 == 0) {
                            $line[] = $j % 2 == 0 ? $opt[1] : $opt[0];
                        } else {
                            $line[] = $j % 2 == 0 ? $opt[0] : $opt[1];
                        }
                    } else {
                        if ($i % 2 == 0) {
                            $line[] = $j % 2 == 0 ? $opt[0] : $opt[1];
                        } else {
                            $line[] = $j % 2 == 0 ? $opt[1] : $opt[0];
                        }
                    }
                }
                $stroke = array_merge([$num[$i-1]], $line, [$num[$i-1]]);
            }
            $rows[] = $stroke;
        }

        return $rows;
    }

    public function getFigures() : array
    {
        $return = [];
        foreach($this->players as $i => $player) {
            $return[$player->get('color')] = [];
            foreach($player->getFigures() as $figure) {
                $return[$player->get('color')][] = [
                    'id' => $figure->getId(),
                    'class' => strtolower(basename(get_class($figure))),
                    'x' => $figure->getX(),
                    'y' => $figure->getY(),
                ];
            }
        }
        return $return;
    }

    public function deleteFigure(int $id)
    {
        $this->pdo->query("DELETE FROM figure WHERE id = $id");
    }

    public function check(int $id, int $figureId = null)
    {
        $figureId = $figureId ? $figureId : 'NULL';
        $this->pdo->query("UPDATE player SET `check` = $figureId WHERE player.id = $id");
    }

    public function end() {
        $date = time();
        $this->pdo->query("UPDATE game SET status = 0, end_time = $date WHERE id = $this->gameId");
    }

    public function delete()
    {
        $stmt = $this->pdo->query("SELECT * FROM player WHERE game_id = $this->gameId");
        $players = $stmt->fetchAll();
        foreach($players as $player) {
            $this->pdo->query("DELETE FROM figure WHERE player_id = $player[id]");
        }
        $this->pdo->query("DELETE FROM player WHERE game_id = $this->gameId");
        $this->pdo->query("DELETE FROM game WHERE id = $this->gameId");
    }


    /**
     * @param int $figureId
     * @param int $x
     * @param int $y
     * @return array
     * @throws Exception
     */
    public function action(int $figureId, int $x, int $y) : array
    {
        $currentFigure = null;
        $allies = [];
        $enemies = [];

        foreach ($this->players as $player) {
            if ($player->get('color') == $this->active) {
                $allies = $player->getFigures();
                $currentFigure = $player->getFigure($figureId);
            } else {
                $enemies = $player->getFigures();
            }
        }
        if(!$currentFigure || !$allies || !$enemies) {
            throw new Exception("Something went wrong");
        }

        if($currentFigure->move($x, $y, $allies, $enemies)) {
            $this->active = $this->active == 'white' ? 'black' : 'white';
        }

        $output = $this->output;
        $this->output = [];
        return $output;
    }

    public function getActive () {
        return $this->active;
    }
}
