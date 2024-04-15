<?php

namespace App\Card;

class CardHand
{
    private $hand = [];
    private $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function add(Card $card): void
    {
        $this->hand[] = $card;
    }

    public function showHand(): array
    {
        $hand_array = [];
        foreach ($this->hand as $card) {
            $hand_array[] = $card->showCard();
        }

        return $hand_array;
    }

    public function cardsInHand(): int
    {
        return count($this->hand);
    }
}
