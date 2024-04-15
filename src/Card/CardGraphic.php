<?php

namespace App\Card;

class CardGraphic extends Card
{
    public function __construct($color, $value)
    {
        parent::__construct($color, $value);
    }

    protected function getAltString(): string
    {
        return parent::getAsShortString();
    }

    protected function getCardFile(): string
    {
        $cardfile = "img/cards/" . $this->colors[$this->color] . "_" . $this->value + 1 .".svg";
        return $cardfile;
    }

    public function showCard(): array
    {
        // Return array of text representation and graphic representation
        return [$this->getAltString(), $this->getCardFile()];
    }

    public function showBack(): array
    {
        // Return array of text representation and graphic representation
        $back = "[X-X]";
        $backfile = "img/cards/back.svg";
        return [$back, $backfile];
    }
}
