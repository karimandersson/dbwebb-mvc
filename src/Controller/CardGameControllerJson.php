<?php

namespace App\Controller;

use App\Card\Card;
use App\Card\CardGraphic;
use App\Card\DeckOfCards;
use App\Card\CardHand;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class CardGameControllerJson extends AbstractController
{
    /*
    #[Route("/api/deck/draw", name: "api_card_draw")]
    #[Route("/api/deck/draw/:number", name: "api_card_draw_number")]
    */

    #[Route("/api/deck", name: "api_card_deck")]
    public function deck(
        SessionInterface $session
    ): Response {
        // Get deck, init if empty
        /** @var DeckOfCards $deck */
        $deck = $session->get("card_deck");
        if ($deck == null) {
            $deck = new DeckOfCards();
            $deck->shuffle();
            $session->set("card_deck", $deck);
        }

        // New init deck. Do not shuffle!
        $initDeck = new DeckOfCards();

        $data = [
            "deck" => $deck->showDeck(),
            "init_deck" => $initDeck->showDeck()
        ];

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    #[Route("/api/deck/shuffle", name: "api_card_deck_shuffle", methods: ['POST'])]
    public function deckShuffle(
        SessionInterface $session
    ): Response {
        // Get deck, init if empty
        /** @var DeckOfCards $deck */
        $deck = $session->get("card_deck");
        if ($deck == null) {
            $deck = new DeckOfCards();
            $deck->shuffle();
            $session->set("card_deck", $deck);
        }

        // If deck is empty, reintialize
        if ($deck->cardsInDeck() == 0) {
            unset($deck);
            $deck = new DeckOfCards();
            $deck->shuffle();
            $session->set("card_deck", $deck);
        }

        // Shuffle deck
        $deck->shuffle();

        // Save to session
        $session->set("card_deck", $deck);

        $data = [
            "deck" => $deck->showDeck()
        ];

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    #[Route("/api/deck/draw", name: "api_card_draw", methods: ['POST'])]
    public function draw(
        SessionInterface $session
    ): Response {
        $num = 1;

        // Get deck, init if empty
        /** @var DeckOfCards $deck */
        $deck = $session->get("card_deck");
        if ($deck == null) {
            $deck = new DeckOfCards();
            $deck->shuffle();
            $session->set("card_deck", $deck);
        }

        $drawedCards = [];
        $message = 'You can\'t draw more cards than there is in the deck.';
        if ($num <= $deck->cardsInDeck()) {
            $message = '';
            $drawed = $deck->draw($num);
            foreach ($drawed as $card) {
                if ($card !== null) {
                    $drawedCards[] = $card->showCard();
                }
            } 
        }

        $data = [
            "drawed" => $drawedCards,
            "cards_left" => $deck->cardsInDeck(),
            "messasge" => $message
        ];

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    #[Route("/api/deck/draw/{num<\d+>}", name: "api_card_draw_num", methods: ['POST'])]
    public function drawNum(
        SessionInterface $session,
        // If I set $num to 1, it will be redirect error, but default value can't
        // be omitted, because then it will be error of not having all parameters
        int $num = 0
    ): Response {
        // Get deck, init if empty
        /** @var DeckOfCards $deck */
        $deck = $session->get("card_deck");
        if ($deck == null) {
            $deck = new DeckOfCards();
            $deck->shuffle();
            $session->set("card_deck", $deck);
        }

        $drawedCards = [];
        $message = 'You can\'t draw more cards than there is in the deck.';
        if ($num == 0) {
            $message = 'You can\'t draw 0 cards.';
        } elseif ($num <= $deck->cardsInDeck()) {
            $message = '';
            $drawed = $deck->draw($num);
            foreach ($drawed as $card) {
                if ($card !== null) {
                    $drawedCards = $card->showCard();
                }
            } 
        }

        $data = [
            "drawed" => $drawedCards,
            "cards_left" => $deck->cardsInDeck(),
            "messasge" => $message
        ];

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    #[Route("/api/deck/deal/{numPlayers<\d+>}/{numCards<\d+>}", name: "api_card_deal")]
    public function deal(
        SessionInterface $session,
        // If I set $num to 1, it will be redirect error, but default value can't
        // be omitted, because then it will be error of not having all parameters
        int $numPlayers = 0,
        int $numCards = 0
    ): Response {
        // Get deck, init if empty
        /** @var DeckOfCards $deck */
        $deck = $session->get("card_deck");
        if ($deck == null) {
            $deck = new DeckOfCards();
            $deck->shuffle();
            $session->set("card_deck", $deck);
        }

        // Init some variables
        $message = '';
        $hands = [];

        // Do some controlls, and send to home with message if not fullfilled
        if ($numCards == 0 || $numPlayers == 0) {
            $message = 'You can\'t draw 0 cards and can\'t have 0 players.';

            $data = [
                "message" => $message,
                "deck" => $deck->showDeck(),
                "hands" => $hands
            ];
    
            $response = new JsonResponse($data);
            $response->setEncodingOptions(
                $response->getEncodingOptions() | JSON_PRETTY_PRINT
            );
            return $response;
        } elseif ($numCards * $numPlayers > $deck->cardsInDeck()) {
            $message = 'You can\'t draw more cards than there is in the deck (' . $deck->cardsInDeck() . ').';

            $data = [
                "message" => $message,
                "deck" => $deck->showDeck(),
                "hands" => $hands
            ];
    
            $response = new JsonResponse($data);
            $response->setEncodingOptions(
                $response->getEncodingOptions() | JSON_PRETTY_PRINT
            );
            return $response;
        }

        // All controlls are passed

        // Set number of players to session
        $session->set("num_players", $numPlayers);

        // Create players=HandOfCards
        $players = [];
        for ($i = 1; $i <= $numPlayers; $i++) {
            $players[] = new CardHand('Player'.$i);
        }

        // Deal cards
        foreach($players as $player) {
            for ($i = 1; $i <= $numCards; $i++) {
                // Draw 1 card, returns array with 1 card
                /** @var array<Card> $draw */
                $draw = $deck->draw(1);
                $player->add($draw[0]);
            }
        }

        // Save to session
        $session->set("card_deck", $deck);
        $session->set("players", $players);

        // Hands
        foreach($players as $player) {
            $hands[] = [
                "name" => $player->getName(),
                "hand" => $player->showHand()
            ];
        }

        $data = [
            "message" => $message,
            "deck" => $deck->showDeck(),
            "hands" => $hands
        ];

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

}
