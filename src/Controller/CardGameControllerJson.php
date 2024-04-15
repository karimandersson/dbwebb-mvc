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
        // Get deck or init
        if ($session->get("card_deck") === null) {
            $deck = new DeckOfCards();
            $deck->shuffle();
            $session->set("card_deck", $deck);
        } else {
            $deck = $session->get("card_deck");
        }

        // New init deck. Do not shuffle!
        $init_deck = new DeckOfCards();

        $data = [
            "deck" => $deck->showDeck(),
            "init_deck" => $init_deck->showDeck()
        ];

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    #[Route("/api/deck/shuffle", name: "api_card_deck_shuffle", methods: ['POST'])]
    public function deck_shuffle(
        SessionInterface $session
    ): Response {

        // Get deck or init
        if ($session->get("card_deck") === null) {
            $deck = new DeckOfCards();
            $deck->shuffle();
            $session->set("card_deck", $deck);
        } else {
            $deck = $session->get("card_deck");
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
        Request $request,
        SessionInterface $session
    ): Response {
        $num = 1;

        // Get deck or init
        if ($session->get("card_deck") === null) {
            $deck = new DeckOfCards();
            $deck->shuffle();
            $session->set("card_deck", $deck);
        } else {
            $deck = $session->get("card_deck");
        }

        $message = '';
        if ($num <= $deck->cardsInDeck()) {
            $drawed_cards = $deck->draw($num, false);
        } else {
            $drawed_cards = [];
            $message = 'You can\'t draw more cards than there is in the deck.';
        }

        $data = [
            "drawed" => $drawed_cards,
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
    public function draw_num(
        Request $request,
        SessionInterface $session,
        // If I set $num to 1, it will be redirect error, but default value can't
        // be omitted, because then it will be error of not having all parameters
        int $num = 0
    ): Response {
        // Get deck or init
        if ($session->get("card_deck") === null) {
            $deck = new DeckOfCards();
            $deck->shuffle();
            $session->set("card_deck", $deck);
        } else {
            $deck = $session->get("card_deck");
        }

        $message = '';
        if ($num == 0) {
            $drawed_cards = [];
            $message = 'You can\'t draw 0 cards.';
        } elseif ($num <= $deck->cardsInDeck()) {
            $drawed_cards = $deck->draw($num, false);
        } else {
            $drawed_cards = [];
            $message = 'You can\'t draw more cards than there is in the deck.';
        }

        $data = [
            "drawed" => $drawed_cards,
            "cards_left" => $deck->cardsInDeck(),
            "messasge" => $message
        ];

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    #[Route("/api/deck/deal/{num_players<\d+>}/{num_cards<\d+>}", name: "api_card_deal")]
    public function deal(
        Request $request,
        SessionInterface $session,
        // If I set $num to 1, it will be redirect error, but default value can't
        // be omitted, because then it will be error of not having all parameters
        int $num_players = 0,
        int $num_cards = 0
    ): Response {
        // Get deck or init
        if ($session->get("card_deck") === null) {
            $deck = new DeckOfCards();
            $deck->shuffle();
            $session->set("card_deck", $deck);
        } else {
            $deck = $session->get("card_deck");
        }

        // Init some variables
        $message = '';
        $hands = [];

        // Do some controlls, and send to home with message if not fullfilled
        if ($num_cards == 0 || $num_players == 0) {
            $message = 'You can\'t draw 0 cards and can\'t have 0 players.';
        } elseif ($num_cards * $num_players > $deck->cardsInDeck()) {
            $message = 'You can\'t draw more cards than there is in the deck (' . $deck->cardsInDeck() . ').';
        } else {
            // All controlls are passed

            // Set number of players to session
            $session->set("num_players", $num_players);

            // Create players=HandOfCards
            $players = [];
            for ($i = 1; $i <= $num_players; $i++) {
                $players[] = new CardHand('Player'.$i);
            }

            // Deal cards
            foreach($players as $player) {
                for ($i = 1; $i <= $num_cards; $i++) {
                    // Draw 1 card, returns array with 1 card
                    $draw = $deck->draw(1, true);
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
