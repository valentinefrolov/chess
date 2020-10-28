<?php

namespace Chess\Figure;

class Bishop extends Queen
{
    public function verify(int $x, int $y, array $allies, array $enemies) : bool
    {
        return $this->checkDiagonal($x, $y, $allies, $enemies);
    }

    protected function _attackKing(int $kingX, int $kingY, array $allies, array $enemies): bool
    {
        return $this->checkDiagonal($kingX, $kingY, $allies, $enemies);
    }

    public function canSave(IKing $king, IWarrior $enemy, array $allies, array $enemies): bool
    {
        return $this->canSaveDiagonal($king, $enemy, $allies, $enemies);
    }
}
