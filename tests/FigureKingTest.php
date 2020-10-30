<?php


use Chess\Figure\Bishop;
use Chess\Figure\King;
use Chess\Figure\Knight;
use Chess\Figure\Pawn;
use Chess\Figure\Queen;
use Chess\Figure\Rook;
use Chess\Game;
use Chess\Player;
use PHPUnit\Framework\TestCase;

class FigureKingTest extends TestCase
{
    /** @var Game  */
    protected $game = null;
    /** @var Player  */
    protected $player = null;
    /** @var Player  */
    protected $enemyPlayer = null;

    /** @var King  */
    protected $figure = null;

    /** @var Pawn */
    protected $allyPawn = null;
    /** @var Pawn */
    protected $allyPawn1 = null;

    /** @var Bishop */
    protected $allyBishop = null;
    /** @var Bishop */
    protected $enemyBishop = null;

    function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->game = new Game();
        $this->game->init();

        $this->player = $this->game->addPlayer('test1');
        $this->figure = new King($this->player, 3, 7);
        $this->allyPawn = new Pawn($this->player, 2, 6);
        $this->allyPawn1 = new Pawn($this->player, 3, 6);
        $this->allyBishop = new Bishop($this->player, 2, 7);

        $this->player->createFigures([
            $this->figure,
            $this->allyPawn,
            $this->allyPawn1,
            $this->allyBishop
        ]);

        $this->enemyPlayer = $this->game->addPlayer('test2');

        $this->enemyBishop = new Bishop($this->enemyPlayer, 6, 4);

        $this->enemyPlayer->createFigures([
            $this->enemyBishop
        ]);

    }


    function testVerify() {
        $enemies = $this->enemyPlayer->getFigures();
        $allies = $this->player->getFigures();

        for ($x = 7; $x >= 0; $x--) {
            for($y = 7; $y >= 0; $y--) {
                if(
                    ($x == 4 && $y == 7)
                    || ($x == 4 && $y == 6)
                    || ($x == 3 && $y == 7)
                ) {
                    $this->assertTrue($this->figure->verify($x, $y, $allies, $enemies), "true: x: $x, y: $y");
                } else {
                    $this->assertFalse($this->figure->verify($x, $y, $allies, $enemies), "false: x: $x, y: $y");
                }
            }
        }
    }

    function testLookAround()
    {
        $this->assertFalse($this->figure->lookAround(4, 6, $this->player->getFigures(), $this->enemyPlayer->getFigures()));
        $this->assertTrue($this->figure->lookAround(4, 7, $this->player->getFigures(), $this->enemyPlayer->getFigures()));
    }

    function testCanEscape()
    {
        $allies = $this->player->getFigures();
        $enemies = $this->enemyPlayer->getFigures();

        $this->assertTrue($this->figure->canEscape($this->enemyBishop, $allies, $enemies));
    }

    function tearDown()
    {
        $this->game->delete();
    }


}
