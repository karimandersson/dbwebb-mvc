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
        $deck_array = [];
        foreach ($this->deck as $card) {
            // $deck_array[] = $card->getAsShortString();
            // $deck_array[] = $card->getAsString();
            $deck_array[] = $card->showCard();
        }

        return $deck_array;
    }

    /**
     * @return array<int, Card|array{string, string|null}|null>
     */
    public function draw(int $num = 1, bool $returnObject = false): array
    {
        $drawed_cards = [];
        if ($num > 0 && $num <= count($this->deck)) {
            for ($i = 0; $i < $num; $i++) {
                // Return representation or object of Card
                if ($returnObject) {
                    $drawed_cards[] = array_shift($this->deck);
                } else {
                    $drawed_cards[] = array_shift($this->deck)->showCard();
                }
            }
        }

        return $drawed_cards;
    }

    public function cardsInDeck(): int
    {
        return count($this->deck);
    }
}
