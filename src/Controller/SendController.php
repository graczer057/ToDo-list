<?php
namespace App\Controller;

use App\Form\SendFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;
use App\Security\Authenticator;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Mime\Email;

class SendController extends AbstractController
{
    private $entityManger;
    private $UserRepository;
    private $emailVerifier;
    public function __construct(
        EntityManagerInterface $entityManager,
        UserRepository $UserRepository,
        EmailVerifier $emailVerifier
    ){
        $this->entityManger = $entityManager;
        $this->UserRepository = $UserRepository;
        $this->emailVerifier = $emailVerifier;
    }
    /**
     * @param Request $request
     * @return Response
     * @Route("/email}", name="email_password_change")
     */
    public function expire(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(SendFormType::class);
        $form->handleRequest($request);

        $formData = $form->getData();
        $this->createNotFoundException();

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->UserRepository->findOneBy(['email' => $formData['email']]);
            $date = new \DateTime("now");
            $date->modify ("+15 minutes");
            $token = md5(uniqid(time()));
            $user->setToken($token);
            $user->setDate($date);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $url = $this->generateUrl('change_password', array('token' => $user->getToken()), UrlGenerator::ABSOLUTE_URL);

            $email = (new Email())
                ->from('bartlomiej.szyszkowski@yellows.eu')
                ->to($user->getEmail())
                ->subject('Change your password')
                ->html($url);

            $mailer->send($email);
            $this->addFlash('success', 'We successfully send you an email.');
            return $this->redirectToRoute('homepage');
        }
        return $this->render('send.html.twig', [
            'form' => $form->createView()
        ]);
    }
}