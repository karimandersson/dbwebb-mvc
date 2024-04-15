<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionController extends AbstractController
{
    /*
    * Home
    */
    #[Route("/session", name: "session_show", methods: ['GET'])]
    public function session_home(
        SessionInterface $session
    ): Response {
        $data = [
            "sessionJSON" => json_encode($session->all(), JSON_PRETTY_PRINT),
            "session" => $session->all()
        ];

        return $this->render('general/session.html.twig', $data);
    }

    #[Route("/session/delete", name: "session_delete", methods: ['GET'])]
    public function session_delete(
        SessionInterface $session
    ): Response {
        $session->clear();

        $this->addFlash(
            'notice',
            'The session was cleared!'
        );

        return $this->redirectToRoute('session_show');
    }

}
