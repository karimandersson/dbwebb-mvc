<?php

namespace App\Controller;

use App\Card\Card;
use App\Card\CardGraphic;
use App\Card\DeckOfCards;
use App\Card\CardHand;
use App\Game21\Game21;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Game21Controller extends AbstractController
{
    #[Route("/game", name: "game21_start")]
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

        return $this->render('game21/home.html.twig');
    }

    #[Route("/game/doc", name: "game21_doc")]
    public function doc(): Response {        

        return $this->render('game21/doc.html.twig');
    }

    #[Route("/game/play/", name: "game21_play")]
    public function play(
        SessionInterface $session,
        Request $request
    ): Response {
        // Get deck, init if empty
        /** @var DeckOfCards $deck */
        $deck = $session->get("card_deck");
        if ($deck == null) {
            $deck = new DeckOfCards();
            $deck->shuffle();
            $session->set("card_deck", $deck);
        }

        // Get game or init
        /** @var bool $gameStarted */
        $gameStarted = $session->get('game_started');
        /** @var Game21 $game */
        $game = $session->get('game21');
        if ($gameStarted == null) {
            $game = new Game21($request->query->get('banklevel'));
            $gameStarted = true;
            $session->set("game_started", $gameStarted);
            $session->set("game21", $game);
        }

        $data = [];
        if ($gameStarted) {
            $data = [
                "namePlayer1" => $game->player1GetName()
            ];
        }

        return $this->render('game21/play.html.twig', $data);
    }
}
