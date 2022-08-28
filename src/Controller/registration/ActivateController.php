<?php

namespace App\Controller\registration;

use App\Form\ExpireFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

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
    public function activate(string $token, Request $request, MailerInterface $mailer){
        $date = new \DateTime("now");

        $user = $this->UserRepository->findOneBy(['token' => $token]);

        $form = $this->createForm(ExpireFormType::class, $user);
        $form->handleRequest($request);

        if(is_null($user)){
            return $this->render('landing/homepage.html.twig');
        }

        if($user->getTokenExpire()->getTimestamp() > $date->getTimestamp()){
            $user -> setIsActiveUser('1');
            $user -> setToken(null);
            $user -> setDate(null);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $email = (new Email())
                ->from('bartlomiej.szyszkowski@yellows.eu')
                ->to($user->getEmail())
                ->subject('Gratulacje, konto zostało aktywowane!')
                ->text('Witamy w projekcie TODO. Życzymy Tobie samych owocnych i łatwych tasków!');

        $mailer->send($email);

        $this->addFlash('success', 'Użytkownik poprawnie aktywowany');

        return $this->redirectToRoute('homepage');
        }else{
            return $this->redirectToRoute('expire');
        }
    }
}
