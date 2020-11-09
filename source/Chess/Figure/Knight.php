<?php


namespace Chess\Figure;


class Knight extends AbstractFigure
{

    private function getPositions() : array
    {
        return [
            [ 'x' => $this->x - 2, 'y' => $this->y - 1 ],
            [ 'x' => $this->x - 1, 'y' => $this->y - 2 ],
            [ 'x' => $this->x + 1, 'y' => $this->y - 2 ],
            [ 'x' => $this->x + 2, 'y' => $this->y - 1 ],
            [ 'x' => $this->x - 2, 'y' => $this->y + 1 ],
            [ 'x' => $this->x - 1, 'y' => $this->y + 2 ],
            [ 'x' => $this->x + 1, 'y' => $this->y + 2 ],
            [ 'x' => $this->x + 2, 'y' => $this->y + 1 ],
        ];
    }

    public function verify(int $x, int $y, array $allies, array $enemies) : bool
    {
        foreach($this->getPositions() as $item) {
            if($item['x'] == $x && $item['y'] == $y) {
                if(!$this->checkEmptyCell($x, $y, $allies)) {
                    return false;
                }
                return true;
            }
        }
        return false;
    }

    protected function _attackKing(int $kingX, int $kingY, array $allies, array $enemies): bool
    {
        foreach($this->getPositions() as $item) {
            if($item['x'] == $kingX && $item['y'] == $kingY) {
                return true;
            }
        }
        return false;
    }

    public function canSave(IKing $king, IWarrior $enemy, array $allies, array $enemies): bool
    {
        $initialX = $this->x;
        $initialY = $this->y;

        foreach($this->getPositions() as $item) {
            $this->x = $item['x']; $this->y = $item['y'];
            $res = $enemy->attackKing($king, $enemies, $allies);
            $this->x = $initialX; $this->y = $initialY;
            if(!$res) {
                return true;
            }
        }
        return false;
    }
}
