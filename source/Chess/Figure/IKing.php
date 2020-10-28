<?php


namespace Chess\Figure;


interface IKing
{
    function lookAround(int $x, int $y, array $allies, array $enemies) : bool;
    function canEscape(IWarrior $enemy, array $allies, array $enemies) : bool;
}
