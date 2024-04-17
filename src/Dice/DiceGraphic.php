<?php

namespace App\Dice;

class DiceGraphic extends Dice
{
    /** @var array<string> */
    private array $representation = [
        '⚀',
        '⚁',
        '⚂',
        '⚃',
        '⚄',
        '⚅',
    ];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return string
     */
    public function getAsString(): string
    {
        return (string) $this->representation[$this->value - 1];
    }
}
