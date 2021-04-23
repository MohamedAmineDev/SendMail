<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    /**
     * @Route("/security", name="security")
     */
    public function index(): Response
    {
        return $this->render('security/index.html.twig', [
            'controller_name' => 'SecurityController',
        ]);
    }

    /**
     * @Route("/connexion",name="app_login")
     */
    public function login():Response
    {
        if($this->getUser()){
            return $this->redirectToRoute('send_mail');
        }
        return $this->render('security/login.html.twig');
    }

    /**
     * @Route("/deconnexion",name="app_logout")
     */
    public function logout():Response{
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }
        return $this->render('security');
    }
}
