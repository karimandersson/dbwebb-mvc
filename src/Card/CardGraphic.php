<?php

namespace App\Card;

class CardGraphic extends Card
{
    public function __construct(int $color, int $value)
    {
        parent::__construct($color, $value);
    }

    protected function getAltString(): string
    {
        return parent::getAsShortString();
    }

    protected function getCardFile(): string
    {
        $cardfile = "img/cards/" . $this->colors[$this->color] . "_" . (intval($this->value) + 1) .".svg";
        return $cardfile;
    }

    /**
     * @return array{string, string}
     */
    public function showCard(): array
    {
        // Return array of text representation and graphic representation
        return [$this->getAltString(), $this->getCardFile()];
    }

    /**
     * @return array{string, string}
     */
    public function showBack(): array
    {
        // Return array of text representation and graphic representation
        $back = "[X-X]";
        $backfile = "img/cards/back.svg";
        return [$back, $backfile];
    }
}
