<?php

namespace App\Controller\Accounts;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\Authenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class ExpireController extends AbstractController
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
     * @Route("/expire/{token}", name="app_activate_expire")
     */
    public function expire(string $token, Request $request, GuardAuthenticatorHandler $guardHandler, Authenticator $authenticator, MailerInterface $mailer): Response
    {
        $date = new \DateTime("now");
        $user = $this->UserRepository->findOneBy(['token' => $token]);
        $form = $this->createForm(ExpireFormType::class, $user);
        $form->handleRequest($request);

        if($user->getTokenExpire() > $date->getTimestamp()) {

            if ($form->isSubmitted() && $form->isValid()) {
                $date = new \DateTime("now");
                $date->modify('+120 minutes');
                $token = md5(uniqid(time()));
                $user->setIsActiveUser('0');
                $user->setToken($token);
                $user->setDate($date);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
                // do anything else you need here, like send an email

                $url = $this->generateUrl('app_activate_expire', array('token' => $user->getToken()), UrlGenerator::ABSOLUTE_URL);

                $email = (new Email())
                    ->from('task.bot@yellows.eu')
                    ->to($user->getEmail())
                    ->subject('Activate your account')
                    ->html($url);

                $mailer->send($email);

            }
        }
        return $this->render('expire.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
