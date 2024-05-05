<?php

namespace App\Card;

/**
 * Class for player
 */
class CardHand
{
    /** @var array<Card> */
    private array $hand = [];
    private string $name;
    private int $balance;

    public function __construct(string $name, int $balance = 0)
    {
        $this->name = $name;
        $this->balance = $balance;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getBalance(): int
    {
        return $this->balance;
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
        $handArray = [];
        foreach ($this->hand as $card) {
            $handArray[] = $card->showCard();
        }

        return $handArray;
    }

    public function cardsInHand(): int
    {
        return count($this->hand);
    }

    public function changeBalance($diff): void
    {
        $this->balance += $diff;
    }

    public function deleteHand(): void
    {
        $this->hand = [];
    }
}
