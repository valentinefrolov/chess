<?php

namespace Chess\Figure;

class Queen extends AbstractFigure
{
    public function verify(int $x, int $y, array $allies, array $enemies) : bool
    {
        if(!$this->checkDiagonal($x, $y, $allies, $enemies) && !$this->checkStraight($x, $y, $allies, $enemies)) {
            return false;
        }
        return true;
    }

    protected function _attackKing(int $kingX, int $kingY, array $allies, array $enemies): bool
    {
        if($this->checkDiagonal($kingX, $kingY, $allies, $enemies) || $this->checkStraight($kingX, $kingY, $allies, $enemies)) {
            return true;
        }
        return false;
    }

    protected function checkDiagonal(int $x, int $y, array $allies, array $enemies) : bool
    {
        if(abs($x - $this->x) != abs($y - $this->y)) {
            return false;
        }

        $xStep = $x > $this->x ? +1 : -1;
        $yStep = $y > $this->y ? +1 : -1;

        $i = 0;
        while($i < abs($x - $this->x)-1) {

            $i++;
            if(
                !$this->checkEmptyCell($this->x + ($xStep*$i), $this->y + ($yStep*$i), $allies)
                ||
                (
                    $this->x + ($xStep*$i) != $x && $this->y + ($yStep*$i) != $y &&
                    !$this->checkEmptyCell($this->x + ($xStep*$i), $this->y + ($yStep*$i), $enemies)
                )
            ) {
                return false;
            }
        }
        return true;
    }

    protected function checkStraight(int $x, int $y, array $allies, array $enemies) : bool
    {
        if($x != $this->x && $y != $this->y) {
            return false;
        }

        if($x < $this->x) {
            for($i = $x+1; $i < $this->x; $i++) {
                if(!$this->checkEmptyCell($i, $y, $allies) || !$this->checkEmptyCell($i, $y, $enemies)) {
                    return false;
                }
            }
        } else if($x > $this->x) {
            for($i = $this->x+1; $i < $x; $i++) {
                if(!$this->checkEmptyCell($i, $y, $allies) || !$this->checkEmptyCell($i, $y, $enemies)) {
                    return false;
                }
            }
        } else if($y < $this->y) {
            for($i = $y+1; $i < $this->y; $i++) {
                if(!$this->checkEmptyCell($x, $i, $allies) || !$this->checkEmptyCell($x, $i, $enemies)) {
                    return false;
                }
            }
        } else if($y > $this->y) {
            for($i = $this->y+1; $i < $y; $i++) {
                if(!$this->checkEmptyCell($x, $i, $allies) || !$this->checkEmptyCell($x, $i, $enemies)) {
                    return false;
                }
            }
        }
        return true;
    }

    public function canSaveStraight(IKing $king, IWarrior $enemy, array $allies, array $enemies): bool
    {
        $initialX = $this->x;
        $initialY = $this->y;

        for($x = $this->x-1; $x >= 0; $x--) {
            if($this->verify($x, $initialY, $allies, $enemies)) {
                $this->x = $x;
                $res = $enemy->attackKing($king, $enemies, $allies);
                $this->x = $initialX;
                if(!$res) {
                    return true;
                }
            } else {
                break;
            }
        }

        for($x = $this->x+1; $x <= 7; $x++) {
            if($this->verify($x, $initialY, $allies, $enemies)) {
                $this->x = $x;
                $res = $enemy->attackKing($king, $enemies, $allies);
                $this->x = $initialX;
                if(!$res) {
                    return true;
                }
            } else {
                break;
            }
        }

        for($y = $this->y-1; $y >= 0; $y--) {
            if($this->verify($initialX, $y, $allies, $enemies)) {
                $this->y = $y;
                $res = $enemy->attackKing($king, $enemies, $allies);
                $this->y = $initialY;
                if(!$res) {
                    return true;
                }
            } else {
                break;
            }
        }

        for($y = $this->y+1; $y <= 7; $y++) {
            if($this->verify($initialX, $y, $allies, $enemies)) {
                $this->y = $y;
                $res = $enemy->attackKing($king, $enemies, $allies);
                $this->y = $initialY;
                if(!$res) {
                    return true;
                }
            } else {
                break;
            }
        }
        return false;
    }

    public function canSaveDiagonal(IKing $king, IWarrior $enemy, array $allies, array $enemies): bool
    {
        $initialX = $this->x;
        $initialY = $this->y;

        $leftDim = $this->x >= $this->y ? $this->y : $this->x;
        $rightDim = $this->x >= $this->y ? $this->x : $this->y;

        $x = $initialX; $yt = $initialY; $yb = $initialY;
        for($i = $leftDim-1; $i >= 0; $i--) {
            if($this->verify(--$x, --$yt, $allies, $enemies)) {
                $this->x = $x; $this->y = $yt;
                $res = $enemy->attackKing($king, $enemies, $allies);
                $this->x = $initialX; $this->y = $initialY;
                if(!$res) {
                    return true;
                }
            }
            if($this->verify($x, ++$yb, $allies, $enemies)) {
                $this->x = $x; $this->y = $yb;
                $res = $enemy->attackKing($king, $enemies, $allies);
                $this->x = $initialX; $this->y = $initialY;
                if(!$res) {
                    return true;
                }
            }
        }

        $x = $initialX; $yt = $initialY; $yb = $initialY;
        for($i = $rightDim+1; $i <= 7; $i++) {
            if($this->verify(++$x, --$yt, $allies, $enemies)) {
                $this->x = $x; $this->y = $yt;
                $res = $enemy->attackKing($king, $enemies, $allies);
                $this->x = $initialX; $this->y = $initialY;
                if(!$res) {
                    return true;
                }
            }
            if($this->verify($x, ++$yb, $allies, $enemies)) {
                $this->x = $x; $this->y = $yb;
                $res = $enemy->attackKing($king, $enemies, $allies);
                $this->x = $initialX; $this->y = $initialY;
                if(!$res) {
                    return true;
                }
            }
        }
        return false;
    }

    public function canSave(IKing $king, IWarrior $enemy, array $allies, array $enemies): bool
    {
        if($this->canSaveStraight($king, $enemy, $allies, $enemies) || $this->canSaveDiagonal($king, $enemy, $allies, $enemies)) {
            return true;
        }
        return false;
    }
}
