<?php

namespace App\Controller;

use Exception;
use App\Entity\User;
use App\Form\ForgottenPasswordType;
use App\Form\ResetPasswordType;
use App\Form\UserRegistrationType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/registration", name="registration")
     */
    public function index(): Response
    {
        return $this->render('registration/index.html.twig', [
            'controller_name' => 'RegistrationController',
        ]);
    }


    /**
     * @Route("/inscription",name="app_registration")
     */
    public function newUser(Request $request, EntityManagerInterface $entityManager,UserPasswordEncoderInterface $encoder,TokenGeneratorInterface $tokenGenerator,MailerInterface $mailer): Response
    {
        $user = new User();
        $form = $this->createForm(UserRegistrationType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // hasher le mot de passe récupéré par le formulaire
            $encodedPassword=$encoder->encodePassword($user,$user->getPassword());
            $user->setPassword($encodedPassword);
            // générer un token d'activation de compte
            $codeToken=$tokenGenerator->generateToken();
            $user->setActivationToken($codeToken);
            $user->setRoles(['Developpeur']);
            // sauvegarder le nouveau utilisateur
            $entityManager->persist($user);
            $entityManager->flush();
            // envoyer un mail
            $email=(new Email )
            ->from('system@gmail.com')
            ->to($user->getEmail())
            ->subject('Account Activation !')
            ->text('Hey there thank you for subscribing to our website.')
            ->html('<p>you will find in this mail a link to activate your account <a href="https://localhost:8000/activation/'.$codeToken.'">link</a></p><p>Have a good day.')
            ;
            $mailer->send($email);
            $this->addFlash('success','Le mail est envoyé avec succès');
            return $this->redirectToRoute('send_mail');
        }
        return $this->render('registration/registration.html.twig',[
            'form'=>$form->createView()
        ]);
    }


    /**
     * @Route("/activation/{token}",name="app_account_activation")
     */
    public function activateUser(UserRepository $userRepository,$token,EntityManagerInterface $entityManager){

        $user=$userRepository->findOneBy(['activationToken'=>$token]);
        if ($user) {
            $user->setActivationToken(null);
            $entityManager->flush();
            $this->addFlash('info','your account is now active');
        }
        else{
            return $this->createNotFoundException('this account does\'nt exist ! ');
        }
     return $this->redirectToRoute('send_mail');
    }

    /**
     * @Route("/motdepasse_oublié",name="app_account_prepare_to_reset_password")
     */
    public function askToRenewPassword(Request $request,UserRepository $userRepository,TokenGeneratorInterface $tokenGenerator,MailerInterface $mailer,EntityManagerInterface $entityManager):Response
    {
        $form=$this->createForm(ForgottenPasswordType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $e_mail=$form->get('email')->getData();
            $user=$userRepository->findOneBy(['email'=>$e_mail]);
            if($user){
                // on va modifier l'entité en ajoutant le resetToken qui indique le mot de passe à été oublié
                $codeToken=$tokenGenerator->generateToken();
                $user->setResetToken($codeToken);
                $entityManager->flush();
                // on va envoyer un mail qui comporte un lien pour changer de mot de passe
                $email=(new Email )
                ->from('system@gmail.com')
                ->to($user->getEmail())
                ->subject('Account Activation !')
                ->text('Hey there thank you for telling us that you have forgot you password')
                ->html('<p>you will find in this mail a link to reset your password <a href="https://localhost:8000/changer_password/'.$codeToken.'">link</a></p><p>Have a good day.')
                ;
                $mailer->send($email);
                $this->addFlash('success','Le mail est envoyé avec succès');
                return $this->redirectToRoute('send_mail');
            }
        }
        return $this->render('registration/password_forgotten.html.twig',[
            'form'=>$form->createView()
        ]);
    }

    /**
     * @Route("/changer_password/{token}",name="app_password_reset")
     */
    public function resetPassword(UserPasswordEncoderInterface $encoder,Request $request,UserRepository $userRepository,MailerInterface $mailer,$token):Response
    {
       $form=$this->createForm(ResetPasswordType::class);
       $form->handleRequest($request);
       $user=$userRepository->findOneBy(['resetToken'=>$token]);
       if($user){
        if($form->isSubmitted() && $form->isValid()){
           $newPassword=$form->get('password')->getData();
           $newEncodedPassword=$encoder->encodePassword($user,$newPassword);
           $user->setResetToken(null);
           $userRepository->upgradePassword($user,$newEncodedPassword);
           $this->addFlash('success','Le mail est envoyé avec succès');
                return $this->redirectToRoute('app_login');
        }
         return $this->render('registration/reset_password.html.twig',[
             'form'=>$form->createView()
         ]);
       }
       return $this->redirectToRoute('send_mail');

    }
}
