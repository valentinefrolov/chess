<?php

namespace Chess\Figure;

use Chess\Game;
use Chess\Player;

abstract class AbstractFigure implements IWarrior
{
    const ACTION_MOVE = 'move';
    const ACTION_ERROR = 'error';
    const ACTION_KILL = 'kill';
    const ACTION_CHECK = 'check';
    const ACTION_MATE = 'mate';

    /** @var Game  */
    protected $game = null;
    /** @var Player  */
    protected $player = null;

    /** @var int  */
    protected $id = 0;
    /** @var int  */
    protected $x = -1;
    /** @var int  */
    protected $y = -1;

    public function __construct(Player $player, int $x, int $y)
    {
        $this->player = $player;
        $this->game = $this->player->get('game');
        $this->x = $x;
        $this->y = $y;
    }

    abstract public function verify(int $x, int $y, array $allies, array $enemies) : bool;
    abstract protected function _attackKing(int $kingX, int $kingY, array $allies, array $enemies) : bool;

    public function attackKing(IKing $king, array $allies, array $enemies, int $x = null, int $y = null): bool
    {
        /** @var King|IKing $king */
        if(!$x) $x = $king->getX();
        if(!$y) $y = $king->getY();
        return $this->_attackKing($x, $y, $allies, $enemies);
    }

    public function setId(int $id) {
        if($this->id == null) {
            $this->id = $id;
        }
    }

    public function getId() {
        return $this->id;
    }

    public function getX() {
        return $this->x;
    }

    public function getY() {
        return $this->y;
    }

    protected function output(string $action = null)
    {
        $this->game->output($this->getId(), $action, $this->x, $this->y);
    }

    protected function checkEmptyCell(int $x, int $y, array $items) : bool
    {
        foreach($items as $item) {
            /** @var AbstractFigure $item */
            if($item->getX() == $x && $item->getY() == $y) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param int $x
     * @param int $y
     * @param array $allies
     * @param array $enemies
     * @return bool
     * @throws \Exception
     */
    public function move(int $x, int $y, array $allies, array $enemies) : bool {
        // base check for no worries in implements
        if(($x == $this->x && $y == $this->y) || $x > 7 || $y > 7 || $x < 0 || $y < 0) {
            $this->output(static::ACTION_ERROR);
            return false;
        }
        // can not move to cell that taken allies figure
        if(!$this->checkEmptyCell($x, $y, $allies)) {
            $this->output(static::ACTION_ERROR);
            return false;
        }

        // implementing
        if(!$this->verify($x, $y, $allies, $enemies)) {
            $this->output(static::ACTION_ERROR);
            return false;
        }

        // verify for existing check
        if($this->player->get('check')) {
            $initialX = $this->x;
            $initialY = $this->y;
            $this->x = $x;
            $this->y = $y;
            foreach($allies as $ally) {
                if($ally instanceof IKing) {
                    $king = $ally;
                    foreach($enemies as $enemy) {
                        if($enemy->x != $this->x && $enemy->y != $this->y && $enemy->attackKing($king, $enemies, $allies)) {
                            $this->x = $initialX;
                            $this->y = $initialY;
                            $this->output(static::ACTION_ERROR);
                            return false;
                        }
                    }
                }
            }
            $this->player->check(null);
        }

        // checking to own check
        if($this instanceof IKing) {
            if(!$this->lookAround($x, $y, $allies, $enemies)) {
                $this->output(static::ACTION_ERROR);
                return false;
            }
        }

        // checking to kill
        foreach($enemies as $enemy) {
            /** @var AbstractFigure $enemy */
            if($enemy->getX() == $x && $enemy->getY() == $y) {
                $enemy->output(static::ACTION_KILL);
                $enemy->player->deleteFigure($enemy->id);
            }
        }


        $this->x = $x;
        $this->y = $y;

        // verify for new check
        foreach($enemies as $enemy) {
            if($enemy instanceof IKing) {
                if($this->attackKing($enemy, $allies, $enemies)) {
                    $enemy->output(static::ACTION_CHECK);
                    $enemy->player->check($this->id);
                    if(!$enemy->canEscape($this, $enemies, $allies)) {
                        $enemy->output(static::ACTION_MATE);
                        $this->game->end();
                    }
                }
            }
        }

        $this->output(static::ACTION_MOVE);
        return true;
    }

}
