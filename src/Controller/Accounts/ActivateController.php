<?php

namespace App\Controller\Accounts;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Form\ExpireFormType;
use App\Security\Authenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Mime\Email;


class ActivateController extends AbstractController
{

    private $entityManager;
    private $UserRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserRepository $UserRepository
    ){
        $this->entityManager = $entityManager;
        $this->UserRepository = $UserRepository;
    }

    /**
     * @Route("/activate/{token}", name="app_activate_active")
     */

    public function activate(string $token, Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, Authenticator $authenticator, MailerInterface $mailer): Response{

        $date = new \DateTime("now");
        $user = $this->UserRepository->findOneBy(['token' => $token]);
        $form = $this->createForm(ExpireFormType::class, $user);
        $form->handleRequest($request);
        if(is_null($user)){
            return $this->render('base.html.twig');
        }
        dump($user->getTokenExpire()->getTimestamp() > $date->getTimestamp());

        dump($user->getTokenExpire());
        dump($date->getTimestamp());
        if($user->getTokenExpire()->getTimestamp() > $date->getTimestamp()){
            $user -> setIsActiveUser('1');
            $user -> setToken(null);
            $user -> setDate(null);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $email = (new Email())
                ->from('bartlomiej.szyszkowski@yellows.eu')
                ->to($user->getEmail())
                ->subject('Congrats! You are registered on our website!')
                ->text('Welcome, dear User. We glad you join our family of ToDo Project. On this page you can easily start making some of your tasks in dedicated time. Have a nice day!');

        $mailer->send($email);
        $this->addFlash('success', 'user activ');
        return $this->redirectToRoute('homepage');

        }else{
            return $this->redirectToRoute('expire');
        }
    }
}
