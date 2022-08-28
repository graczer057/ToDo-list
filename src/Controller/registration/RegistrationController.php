<?php

namespace App\Controller\registration;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ){
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/register", name="app_register", methods={"GET", "POST"})
     */
    public function register(Request $request, MailerInterface $mailer, UserPasswordEncoderInterface $passwordEncoder)
    {
        $form = $this->createForm(RegistrationFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = new User();

            $user->setEmail($form->get('email')->getData());
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $date = new \DateTime("now");
            $date->modify('+1 minutes');
            $token=md5(uniqid(time()));
            $user->setIsActiveUser('0');
            $user->setToken($token);
            $user->setDate($date);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $url = $this->generateUrl('app_activate_active', array('token' => $user->getToken()), UrlGenerator::ABSOLUTE_URL);

            $email = (new Email())
                ->from('bartlomiej.szyszkowski@yellows.eu')
                ->to($user->getEmail())
                ->subject('Aktywuj swoje konto.')
                ->html($url);

            $mailer->send($email);

            return $this->redirectToRoute('homepage');
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
