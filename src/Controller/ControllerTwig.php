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

        $lucky1 = random_int(0, count($trains) -1);
        do {
            $lucky2 = random_int(0, count($trains) -1);
        } while ($lucky2 == $lucky1);

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
                "text" => "/api/quote : Dagens citat"
            ]
        ];

        $data = [];
        $data['apilist'] = $apilist;

        return $this->render('general/api.html.twig', $data);
    }
}