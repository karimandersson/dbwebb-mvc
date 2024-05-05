<?php

namespace App\Game21;

use App\Card\CardGraphic;
use App\Card\CardHand;
use App\Card\DeckOfCards;

class Game21
{
    private array $values = [
        "A" => [1, 14],
        "2" => 2,
        "3" => 3,
        "4" => 4,
        "5" => 5,
        "6" => 6,
        "7" => 7,
        "8" => 8,
        "9" => 9,
        "10" => 10,
        "J" => 11,
        "Q" => 12,
        "K" => 13,
    ];

    private CardHand $player1;
    private CardHand $bank;

    private int $bankLevel;
    // Possible levels of the bank
    private array $levels = [1];

    private array $usedCards;

    // private DeckOfCards $deck;

    public function __construct($bankLevel = 1)
    {
        // Set level
        (int) $level = 1;
        if (in_array($bankLevel, $this->levels)) {
            $level = $bankLevel;
        }
        $this->bankLevel = $level;

        // New CardHand for bank
        $this->bank = new CardHand('Bank');
        // New CardHand for player1
        $this->player1 = new CardHand('Player1', 100);
    }

    public function player1GetName(): string
    {
        return $this->player1->getName();
    }

    public function newRound(): void
    {
        // Empty hands
        $this->bank->deleteHand();
        $this->player1->deleteHand();        
    }

}