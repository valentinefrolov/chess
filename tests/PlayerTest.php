<?php


use Chess\Figure\King;
use Chess\Figure\Pawn;
use Chess\Figure\Queen;
use Chess\Game;
use Chess\Player;
use PHPUnit\Framework\TestCase;

class PlayerTest extends TestCase
{
    /** @var Player  */
    private $player = null;
    /** @var Player  */
    private $enemyPlayer = null;
    /** @var Game  */
    private $game = null;
    /** @var King  */
    private $king = null;
    /** @var Pawn  */
    private $pawn = null;
    /** @var Queen  */
    private $queen = null;

    function setUp()
    {
        parent::setUp();
        $this->game = new Game();
        $this->game->init();

        $this->enemyPlayer = $this->game->addPlayer('enemy');
        $this->player = $this->game->addPlayer('player');

        $this->king = new King($this->player, 0, 0);
        $this->pawn = new Pawn($this->player, 1, 1);
        $this->queen = new Queen($this->enemyPlayer, 1, 7);

        $this->player->createFigures([$this->king, $this->pawn]);
        $this->enemyPlayer->createFigures([$this->queen]);

        $this->game->start();
    }

    function testCheck()
    {
        $this->game->action($this->queen->getId(), 0, 7);
        $this->assertTrue((bool)$this->player->get('check'));

        $output = $this->game->action($this->pawn->getId(), 1, 2);
        $this->assertEquals($output[0]['action'], 'error');
        $this->assertTrue((bool)$this->player->get('check'));

        $this->game->action($this->king->getId(), 1, 0);
        $this->assertFalse((bool)$this->player->get('check'));

    }

    function tearDown()
    {
        $this->game->delete();
    }
}
