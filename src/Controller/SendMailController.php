<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SendMailController extends AbstractController
{
    /**
     * @Route("/send/mail", name="send_mail")
     */
    public function index(): Response
    {
        return $this->render('send_mail/index.html.twig', [
            'controller_name' => 'SendMailController',
        ]);
    }
}
