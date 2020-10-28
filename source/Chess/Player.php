<?php

namespace Chess;

use Chess\Figure\AbstractFigure;
use Chess\Figure\Bishop;
use Chess\Figure\King;
use Chess\Figure\Knight;
use Chess\Figure\Pawn;
use Chess\Figure\Queen;
use Chess\Figure\Rook;

class Player
{
    /** @var string  */
    protected $name = '';
    /** @var string  */
    protected $color = '';
    /** @var AbstractFigure[]  */
    protected $figures = [];
    /** @var int */
    protected $id = -1;
    /** @var int */
    protected $check = 0;
    /** @var Game  */
    protected $game = null;
    /** @var bool  */
    protected $atop = false;

    public function __construct(array $data, Game $game, bool $atop = false)
    {
        $this->game = $game;
        $this->atop = $atop;

        foreach($data as $key => $value) {
            if(property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    public function createFigures(array $figures = null)
    {
        if(!$figures) {
            $indexes = $this->atop ? [0, 1] : [7, 6];
            $figures = [
                new Rook($this, 0, $indexes[0]),
                new Knight($this, 1, $indexes[0]),
                new Bishop($this, 2, $indexes[0]),
                new Queen($this, 3, $indexes[0]),
                new King($this, 4, $indexes[0]),
                new Bishop($this, 5, $indexes[0]),
                new Knight($this, 6, $indexes[0]),
                new Rook($this, 7, $indexes[0]),
                new Pawn($this, 0, $indexes[1]),
                new Pawn($this, 1, $indexes[1]),
                new Pawn($this, 2, $indexes[1]),
                new Pawn($this, 3, $indexes[1]),
                new Pawn($this, 4, $indexes[1]),
                new Pawn($this, 5, $indexes[1]),
                new Pawn($this, 6, $indexes[1]),
                new Pawn($this, 7, $indexes[1]),
            ];
        }
        $this->figures = $figures;
    }


    public function atop() {
        return $this->atop;
    }

    public function get(string $key) {
        if(property_exists($this, $key)) {
            return $this->$key;
        }
        return null;
    }

    /**
     * @return AbstractFigure[];
     */
    public function getFigures() : array
    {
        return $this->figures;
    }

    public function getFigure(int $figureId) : ?AbstractFigure
    {
        foreach($this->figures as $figure) {
            if($figure->getId() == $figureId) {
                return $figure;
            }
        }
        return null;
    }

    public function deleteFigure(int $figureId) : void
    {
        foreach($this->figures as $i => $figure) {
            if($figure->getId() == $figureId) {
                unset($this->figures[$i]);
                $this->figures = array_values($this->figures);
                $this->game->deleteFigure($figureId);
            }
        }
    }

    public function check(int $figureId = null) : void
    {
        $this->check = $figureId;
        $this->game->check($this->id, $figureId);
    }

}
