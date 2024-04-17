<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ControllerTwig extends AbstractController
{
    // #[Route("/lucky/number/twig", name: "lucky_number")]
    // public function number(): Response
    // {
    //     $number = random_int(0, 100);

    //     $data = [
    //         'number' => $number
    //     ];

    //     return $this->render('lucky_number.html.twig', $data);
    // }

    #[Route("/", name: "home")]
    public function home(): Response
    {
        return $this->render('general/home.html.twig');
    }

    #[Route("/about", name: "about")]
    public function about(): Response
    {
        return $this->render('general/about.html.twig');
    }

    #[Route("/report", name: "report")]
    public function report(): Response
    {
        return $this->render('general/report.html.twig');
    }

    #[Route("/lucky", name: "lucky")]
    public function lucky(): Response
    {
        $trains = [
            'pic02',
            'pic03',
            'pic04',
            'pic05',
            'pic08',
            'pic10',
            'pic11',
            'pic12'
        ];

        $lucky1 = random_int(0, count($trains) - 1);
        do {
            $lucky2 = random_int(0, count($trains) - 1);
        } while ($lucky2 == $lucky1);

        $data = [];
        $data["pic1"] = $trains[$lucky1];
        $data["pic2"] = $trains[$lucky2];

        return $this->render('general/lucky.html.twig', $data);
    }

    #[Route("/api", name: "apilist")]
    public function apilist(): Response
    {
        // List of api:s
        $apilist = [
            [
                "url" => "./api/quote",
                "routename" => "apiquote",
                "text" => "Dagens citat",
                "method" => "get",
            ],
            [
                "url" => "./api/deck",
                "routename" => "api_card_deck",
                "text" => "Visar kortlek, både aktuell och sorterad full kortlek",
                "method" => "get",
            ],
            [
                "url" => "./api/deck/shuffle",
                "routename" => "api_card_deck_shuffle",
                "text" => "Blandar kortleken, och återställer till full storlek vid behov",
                "method" => "post",
                "fields" => [],
                "on_click" => ""
            ],
            [
                "url" => "./api/deck/draw",
                "routename" => "api_card_draw",
                "text" => "Drar 1 kort",
                "method" => "post",
                "fields" => [],
                "on_click" => ""
            ],
            [
                "url" => "./api/deck/draw/:number",
                "routename" => "api_card_draw_num",
                "text" => "Drar :number kort",
                "method" => "post",
                "fields" => [
                    [
                        "text" => "Antal kort",
                        "name" => 'number',
                        "min" => 1,
                        "max" => 52
                    ]
                ],
                "on_click" => "this.form.action='". $this->generateUrl('api_card_draw') ."' + '/' + this.form.number.value"
            ],
            [
                "url" => "./api/deck/deal/:num_players/:num_cards",
                "routename" => "api_card_deal",
                "text" => "Delar ut :num_cards kort till :num_players spelare",
                "method" => "post",
                "fields" => [
                    [
                        "text" => "Antal spelare",
                        "name" => 'num_players',
                        "min" => 1,
                        "max" => 10
                    ],
                    [
                        "text" => "Antal kort",
                        "name" => 'num_cards',
                        "min" => 1,
                        "max" => 52
                    ]
                ],
                "on_click" => "this.form.action='" . $this->generateUrl('api_card_deal') . "' 
                    + '/' + this.form.num_players.value + '/' + this.form.num_cards.value"
            ]
        ];

        $data = [];
        $data['apilist'] = $apilist;

        return $this->render('general/api.html.twig', $data);
    }
}
