<?php


namespace Chess\Figure;

use Chess\Player;

class Pawn extends AbstractFigure
{

    public function verify(int $x, int $y, array $allies, array $enemies) : bool
    {
        $dir = $this->player->atop() ? -1 : 1;
        $diff = $dir*($this->y - $y);

        // check step back
        if(
            ($this->player->atop() && $y <= $this->y)
            ||
            (!$this->player->atop() && $y >= $this->y)
        ) {
            return false;
        }

        $dist = $this->status == 0 ? 2 : 1;

        // checking wrong distance
        if($diff > $dist) {
            return false;
        }

        // cant eat two cell left or right
        if($this->x != $x && $diff > 1) {
            return false;
        }

        // check to eat
        foreach($enemies as $enemy) {
            /** @var AbstractFigure $enemy */
            if($enemy->getX() == $x && $enemy->getY() == $y) {
                if($this->x == $x+1 || $this->x == $x-1) {
                    return true;
                } else {
                    return false;
                }
            }
        }

        // check step left or right
        if($this->x != $x) {
            return false;
        }

        return true;
    }

    protected function _attackKing(int $kingX, int $kingY, array $allies, array $enemies): bool
    {
        $dir = $this->player->atop() ? +1 : -1;
        if(abs($kingX - $this->x) == 1 && $kingY - $dir == $this->y) {
            return true;
        }
        return false;
    }

    /**
     * @param IKing $king
     * @param IWarrior $enemy
     * @param AbstractFigure[] $allies
     * @param AbstractFigure[] $enemies
     * @return bool
     */
    public function canSave(IKing $king, IWarrior $enemy, array $allies, array $enemies): bool
    {
        $initialX = $this->x;
        $initialY = $this->y;

        $y = $this->player->atop() ? $this->y + 1 : $this->y - 1;

        $matrix = [ $this->x - 1, $this->x, $this->x + 1 ];

        foreach($matrix as $x) {
            if($this->verify($x, $y, $allies, $enemies)) {
                $this->x = $x; $this->y = $y;
                $res = $enemy->attackKing($king, $enemies, $allies);
                $this->x = $initialX; $this->y = $initialY;
                if(!$res) {
                    return true;
                }
            }
        }

        return false;
    }
}
