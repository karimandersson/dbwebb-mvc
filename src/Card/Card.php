<?php

namespace App\Card;

class Card
{
    protected $color;
    protected $value;

    protected $colors = ['clubs', 'diamonds', 'hearts', 'spades'];
    protected $values = ['A', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K'];

    public function __construct($color, $value)
    {
        $this->color = $color;
        $this->value = $value;
    }

    public function getColor(): int
    {
        return $this->color;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    protected function getAsString(): string
    {
        return "[{$this->colors[$this->color]}-{$this->values[$this->value]}]";
    }

    protected function getAsRawString(): string
    {
        return "[$this->color}-{$this->value}]";
    }

    protected function getAsShortString(): string
    {
        return "[" . substr($this->colors[$this->color], 0, 1) . "-{$this->values[$this->value]}]";
    }

    public function showCard(): array
    {
        // Return array of text representation and an empty placeholder for graphic representation
        return [$this->getAsShortString(), null];
    }

    public function showBack(): array
    {
        // Return array of text representation and an empty placeholder for graphic representation
        $back = "[X-X]";
        return [$back, null];
    }
}
