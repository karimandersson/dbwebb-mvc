<?php

namespace App\Controller;

use App\Card\Card;
use App\Card\CardGraphic;
use App\Card\DeckOfCards;
use App\Card\CardHand;

use PhpParser\Node\Scalar\MagicConst\Dir;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CardGameController extends AbstractController
{
    #[Route("/game/card", name: "card_start")]
    public function home(
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

        return $this->render('card/home.html.twig');
    }

    #[Route("/game/card/deck", name: "card_deck")]
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
            "init_deck" => $initDeck->showDeck(),
            "deck" => $deck->showDeck(),
            "session" => $session->all()
        ];

        return $this->render('card/deck.html.twig', $data);
    }

    #[Route("/game/card/deck/shuffle", name: "card_deck_shuffle")]
    public function deckShuffle(
        SessionInterface $session
    ): Response {

        $newDeck = false;

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
            $newDeck = true;
            $deck = new DeckOfCards();
            $deck->shuffle();
            $session->set("card_deck", $deck);
        }

        // Shuffle deck
        $deck->shuffle();

        // Save to session
        $session->set("card_deck", $deck);

        if ($newDeck) {
            $this->addFlash(
                'notice',
                'The old deck was empty. You got a new deck.'
            );
        }

        $this->addFlash(
            'notice',
            'The cards have been shuffled.'
        );

        return $this->redirectToRoute('card_deck');
    }

    #[Route("/game/card/deck/draw", name: "card_draw")]
    public function draw(
        Request $request
    ): Response {
        $num = 1;

        // Get number of cards from start page
        if ($request->query->get('num_cards')) {
            $num = $request->query->get('num_cards');
        }

        // return $this->redirect('../deck/draw/' . $num);
        return $this->redirectToRoute('card_draw_num', ['num' => $num]);
    }

    // #[Route("/game/card/deck/draw/{num}", name: "card_draw_num")]
    #[Route("/game/card/deck/draw/{num<\d+>}", name: "card_draw_num")]
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
        if ($num == 0) {
            $this->addFlash(
                'warning',
                'You can\'t draw 0 cards.'
            );
        } elseif ($num <= $deck->cardsInDeck()) {
            $drawed = $deck->draw($num);
            foreach ($drawed as $card) {
                if ($card !== null) {
                    $drawedCards[] = $card->showCard();
                }
            } 
        } elseif ($num > $deck->cardsInDeck()) {
            $this->addFlash(
                'warning',
                'You can\'t draw more cards than there is in the deck.'
            );
        }

        $data = [
            "drawed" => $drawedCards,
            "cards_left" => $deck->cardsInDeck()
        ];

        return $this->render('card/draw.html.twig', $data);
    }

    #[Route("/game/card/deck/deal/{numPlayers<\d+>}/{numCards<\d+>}", name: "card_deal")]
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

        // Do some controlls, and send to home with message if not fullfilled
        if ($numCards == 0 || $numPlayers == 0) {
            $this->addFlash(
                'warning',
                'You can\'t draw 0 cards and can\'t have 0 players.'
            );
            return $this->render('card/home.html.twig');
        } elseif ($numCards * $numPlayers > $deck->cardsInDeck()) {
            $this->addFlash(
                'warning',
                'You can\'t draw more cards than there is in the deck (' . $deck->cardsInDeck() . ').'
            );
            return $this->render('card/home.html.twig');
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
        $hands = [];
        foreach($players as $player) {
            $hands[] = [
                "name" => $player->getName(),
                "hand" => $player->showHand()
            ];
        }

        $data = [
            "deck" => $deck->showDeck(),
            "hands" => $hands
        ];

        return $this->render('card/deal.html.twig', $data);
    }

}
