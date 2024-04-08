<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ControllerJson
{
        // $response = new Response();
        // $response->setContent(json_encode($data));
        // $response->headers->set('Content-Type', 'application/json');
        // return $response;

        // return new JsonResponse($data);

        // $response = new JsonResponse($data);
        // $response->setEncodingOptions(
        //     $response->getEncodingOptions() | JSON_PRETTY_PRINT
        // );
        // return $response;

    #[Route("/api/quote", name: "apiquote")]
    public function jsonQuote(): Response
    {
        // From https://sv.wikiquote.org/wiki/Wikiquote:Veckans_citat
        $quotes = [
            'Allting förändras men ingenting förgås.', // Pythagoras
            'Skillnaden mellan geniet och dumheten är att geniet har sin begränsning.', // Thorvald Gahlin
            'Det vänliga ordet som sägs idag kan bära frukt i morgon.', // Mahatma Gandhi
            'Dessa är mina principer. Om du inte gillar dem har jag andra.' // Groucho Marx
        ];

        $randomNumber = random_int(0, count($quotes) - 1);

        $data = [
            'quote-of-the-day' => $quotes[$randomNumber],
            'date' => date('Y-m-d'),
            'time' => date('H:i:s')
        ];

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

}