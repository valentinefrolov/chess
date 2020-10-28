<?php


namespace Chess\Figure;


interface IWarrior
{
    public function attackKing(IKing $king, array $allies, array $enemies, int $kingX = null, int $kingY = null): bool;
    public function canSave(IKing $king, IWarrior $enemy, array $allies, array $enemies) : bool;
    public function move(int $x, int $y, array $allies, array $enemies) : bool;
}
