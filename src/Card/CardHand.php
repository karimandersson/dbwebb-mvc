<?php

namespace App\Card;

class CardHand
{
    /** @var array<Card> */
    private array $hand = [];
    private string $name;

    public function __construct(string $name)
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

    /**
     * @return array<int, array{string, string|null}>
     */
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
