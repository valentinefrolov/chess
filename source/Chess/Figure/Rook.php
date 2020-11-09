<?php

namespace Chess\Figure;

class Rook extends Queen
{
    public function verify(int $x, int $y, array $allies, array $enemies) : bool
    {
        return $this->checkStraight($x, $y, $allies, $enemies);
    }

    protected function _attackKing(int $kingX, int $kingY, array $allies, array $enemies): bool
    {
        return $this->checkStraight($kingX, $kingY, $allies, $enemies);
    }

    public function canSave(IKing $king, IWarrior $enemy, array $allies, array $enemies): bool
    {
        return $this->canSaveStraight($king, $enemy, $allies, $enemies);
    }

    protected function checkEmptyCell(int $x, int $y, array $items) : bool
    {
        foreach($items as $item) {
            /** @var AbstractFigure $item */
            if($item->player === $this->player && $item instanceof IKing && $item->status === 0 && $this->status === 0) {
                continue;
            } else if($item->getX() == $x && $item->getY() == $y) {
                return false;
            }
        }
        return true;
    }
}
