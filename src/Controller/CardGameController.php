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
        // Get deck or init
        if ($session->get("card_deck") === null) {
            $deck = new DeckOfCards();
            $deck->shuffle();
            $session->set("card_deck", $deck);
        } else {
            $deck = $session->get("card_deck");
        }

        return $this->render('card/home.html.twig');
    }

    #[Route("/game/card/deck", name: "card_deck")]
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
            "init_deck" => $init_deck->showDeck(),
            "deck" => $deck->showDeck(),
            "session" => $session->all()
        ];

        return $this->render('card/deck.html.twig', $data);
    }

    #[Route("/game/card/deck/shuffle", name: "card_deck_shuffle")]
    public function deck_shuffle(
        SessionInterface $session
    ): Response {

        $new_deck = false;

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
            $new_deck = true;
            $deck = new DeckOfCards();
            $deck->shuffle();
            $session->set("card_deck", $deck);
        }

        // Shuffle deck
        $deck->shuffle();

        // Save to session
        $session->set("card_deck", $deck);

        if ($new_deck) {
            $this->addFlash(
                'notice',
                'The old deck was empty. You got a new deck.'
            );
        } else {
            $this->addFlash(
                'notice',
                'The cards have been shuffled.'
            );
        }

        return $this->redirectToRoute('card_deck');
    }

    #[Route("/game/card/deck/draw", name: "card_draw")]
    public function draw(
        Request $request,
        SessionInterface $session
    ): Response {
        // Get number of cards from start page
        if ($request->query->get('num_cards')) {
            $num = $request->query->get('num_cards');
        } else {
            $num = 1;
        }

        // return $this->redirect('../deck/draw/' . $num);
        return $this->redirectToRoute('card_draw_num', ['num' => $num]);
    }

    // #[Route("/game/card/deck/draw/{num}", name: "card_draw_num")]
    #[Route("/game/card/deck/draw/{num<\d+>}", name: "card_draw_num")]
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

        if ($num == 0) {
            $drawed_cards = [];
            $this->addFlash(
                'warning',
                'You can\'t draw 0 cards.'
            );
        } elseif ($num <= $deck->cardsInDeck()) {
            $drawed_cards = $deck->draw($num, false);
        } else {
            $drawed_cards = [];
            $this->addFlash(
                'warning',
                'You can\'t draw more cards than there is in the deck.'
            );
        }

        $data = [
            "drawed" => $drawed_cards,
            "cards_left" => $deck->cardsInDeck()
        ];

        return $this->render('card/draw.html.twig', $data);
    }

    #[Route("/game/card/deck/deal/{num_players<\d+>}/{num_cards<\d+>}", name: "card_deal")]
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

        // Do some controlls, and send to home with message if not fullfilled
        if ($num_cards == 0 || $num_players == 0) {
            $drawed_cards = [];
            $this->addFlash(
                'warning',
                'You can\'t draw 0 cards and can\'t have 0 players.'
            );
            return $this->render('card/home.html.twig');
        } elseif ($num_cards * $num_players > $deck->cardsInDeck()) {
            $this->addFlash(
                'warning',
                'You can\'t draw more cards than there is in the deck (' . $deck->cardsInDeck() . ').'
            );
            return $this->render('card/home.html.twig');
        }
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
