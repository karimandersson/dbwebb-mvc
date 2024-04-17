<?php

namespace App\Card;

class DeckOfCards
{
    /** @var array<Card> */
    private array $deck = [];

    public function __construct()
    {
        for ($color = 0; $color <= 3; $color++) {
            for ($value = 0; $value <= 12; $value++) {
                // $this->deck[] = new Card($color, $value);
                $this->deck[] = new CardGraphic($color, $value);
            }
        }
    }

    public function shuffle(): void
    {
        shuffle($this->deck);
    }

    /**
     * @return array<int, array{string, string|null}>
     */
    public function showDeck(): array
    {
        $deckArray = [];
        foreach ($this->deck as $card) {
            // $deckArray[] = $card->getAsShortString();
            // $deckArray[] = $card->getAsString();
            $deckArray[] = $card->showCard();
        }

        return $deckArray;
    }

    /**
     * @return array<int, Card|array{string, string|null}|null>
     */
    public function draw(int $num = 1, bool $returnObject = false): array
    {
        $drawedCards = [];
        if ($num > 0 && $num <= count($this->deck)) {
            for ($i = 0; $i < $num; $i++) {
                // Return representation or object of Card
                if ($returnObject) {
                    $drawedCards[] = array_shift($this->deck);
                } else {
                    $drawedCards[] = array_shift($this->deck)->showCard();
                }
            }
        }

        return $drawedCards;
    }

    public function cardsInDeck(): int
    {
        return count($this->deck);
    }
}
