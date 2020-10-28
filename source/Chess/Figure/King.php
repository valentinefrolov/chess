<?php


namespace Chess\Figure;


class King extends Queen implements IKing
{

    public function verify(int $x, int $y, array $allies, array $enemies) : bool
    {
        if(abs($x - $this->x) > 1 || abs($y - $this->y) > 1) {
            return false;
        }

        if(!$this->checkDiagonal($x, $y, $allies, $enemies) && !$this->checkStraight($x, $y, $allies, $enemies)) {
            return false;
        }

        return true;
    }


    protected function _attackKing(int $kingX, int $kingY, array $allies, array $enemies): bool
    {
        if(abs($kingX - $this->x) <= 1 && abs($kingY - $this->y) <= 1) {
            return true;
        }
        return false;
    }


    function lookAround(int $x, int $y, array $allies, array $enemies): bool
    {
        foreach($enemies as $enemy) {
            /** @var IWarrior $enemy */
            if($enemy->attackKing($this, $allies, $enemies, $x, $y)) {
                return false;
            }
        }
        return true;
    }


    /**
     * @param IWarrior|AbstractFigure $enemy
     * @param AbstractFigure[] $allies
     * @param AbstractFigure[] $enemies
     * @return bool
     */
    function canEscape(IWarrior $enemy, array $allies, array $enemies): bool
    {
        // check to move self
        $matrix = [
            [ 'x' => $this->x-1, 'y' => $this->y-1 ],
            [ 'x' => $this->x, 'y' => $this->y-1 ],
            [ 'x' => $this->x+1, 'y' => $this->y-1 ],
            [ 'x' => $this->x-1, 'y' => $this->y ],
            /*[ 'x' => $this->x, 'y' => $this->y ],*/
            [ 'x' => $this->x+1, 'y' => $this->y ],
            [ 'x' => $this->x-1, 'y' => $this->y+1 ],
            [ 'x' => $this->x, 'y' => $this->y+1 ],
            [ 'x' => $this->x+1, 'y' => $this->y+1 ],
        ];

        foreach($matrix as $pos) {
            if($this->verify($pos['x'], $pos['y'], $allies, $enemies)) {
                foreach($enemies as $_enemy) {
                    if($_enemy->attackKing($this, $enemies, $allies, $pos['x'], $pos['y'])) {
                        break 2;
                    }
                }
            }
            return false;
        }

        foreach($allies as $ally) {
            if($ally->canSave($this, $enemy, $allies, $enemies)) {
                return true;
            }
        }

        return false;

    }

    public function canSave(IKing $king, IWarrior $enemy, array $allies, array $enemies): bool
    {
        return false;
    }
}
