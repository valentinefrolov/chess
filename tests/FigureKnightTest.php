<?php


use Chess\Figure\King;
use Chess\Figure\Knight;
use Chess\Figure\Rook;
use Chess\Game;
use Chess\Player;
use PHPUnit\Framework\TestCase;

class FigureKnightTest extends TestCase
{
    /** @var Game  */
    protected $game = null;
    /** @var Player  */
    protected $player = null;
    /** @var Player  */
    protected $enemyPlayer = null;
    /** @var Knight  */
    protected $figure = null;
    /** @var King */
    protected $allyKing = null;
    /** @var King */
    protected $enemyKing = null;
    /** @var Rook */
    protected $enemyRook = null;

    function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->game = new Game();
        $this->game->init();

        $this->player = $this->game->addPlayer('test1');
        $this->figure = new Knight($this->player, 3, 5);
        $this->allyKing = new King($this->player, 4, 7);

        $this->player->createFigures([
            $this->figure,
            $this->allyKing
        ]);

        $this->enemyPlayer = $this->game->addPlayer('test2');
        $this->enemyKing = new King($this->enemyPlayer, 2, 3);
        $this->enemyRook = new Rook($this->enemyPlayer, 0, 7);

        $this->enemyPlayer->createFigures([
            $this->enemyKing,
            $this->enemyRook
        ]);

    }

    function testVerify() {
        $enemies = $this->enemyPlayer->getFigures();
        $allies = $this->player->getFigures();

        for ($x = 0; $x < 8; $x++) {
            for($y = 0; $y < 8; $y++) {
                if(
                    ($x == 2 && $y == 3)
                    || ($x == 4 && $y == 3)
                    || ($x == 1 && $y == 4)
                    || ($x == 5 && $y == 4)
                    || ($x == 1 && $y == 6)
                    || ($x == 5 && $y == 6)
                    || ($x == 2 && $y == 7)
                ) {
                    $this->assertTrue($this->figure->verify($x, $y, $allies, $enemies));
                } else {
                    $this->assertFalse($this->figure->verify($x, $y, $allies, $enemies));
                }
            }
        }
    }

    function testCanSave()
    {
        $this->player->check($this->enemyRook->getId());
        $this->assertTrue($this->figure->canSave($this->allyKing, $this->enemyRook,$this->player->getFigures(), $this->enemyPlayer->getFigures()));
    }

    function testAttackKing()
    {
        $this->assertTrue($this->figure->attackKing($this->enemyKing, $this->player->getFigures(), $this->enemyPlayer->getFigures()));
    }

    function tearDown()
    {
        $this->game->delete();
    }


}