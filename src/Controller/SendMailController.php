<?php

namespace App\Controller;

use App\Entity\SendMail;
use App\Form\SendMailType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SendMailController extends AbstractController
{
    /**
     * @Route("/", name="send_mail")
     */
    public function index(Request $req,EntityManagerInterface $manager,MailerInterface $mailer): Response
    {
        $envoie=new SendMail();
        $form=$this->createForm(SendMailType::class,$envoie);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()){
            $email=(new TemplatedEmail())
            ->from($envoie->getSendFrom())
            ->to($envoie->getSendTo())
            ->subject("Test")
            ->htmlTemplate("send_mail/mail.html.twig")
            ->context([
                "message"=>$envoie->getMessage()
            ])
            ;
            $mailer->send($email);
            return $this->redirectToRoute("send_mail");
        }

        return $this->render('send_mail/index.html.twig', [
            'form'=>$form->createView()    
        ]);
    }
}
