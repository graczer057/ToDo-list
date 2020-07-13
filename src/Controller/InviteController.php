<?php
namespace App\Controller;

use App\Form\InviteType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Mime\Email;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class InviteController extends AbstractController
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
     * @param string $email1
     * @param Request $request
     * @return Response
     * @Route("/invite}", name="invite")
     */
    public function invite(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(InviteType::class);
        $form->handleRequest($request);
        $formData = $form->getData();

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $this->UserRepository->findOneBy(['email' => $formData['email']]);

            $url = $this->generateUrl('app_register', array(), UrlGenerator::ABSOLUTE_URL);


            $email = (new Email())
                ->from('bartlomiej.szyszkowski@yellows.eu')
                ->to($formData['email'])
                ->subject('Activate your account')
                ->html($url);

            $mailer->send($email);
            $this->addFlash('success', 'Invite successfully send');
            return $this->redirectToRoute('homepage');
        }
        return $this->render('invite.html.twig', [
            'form' => $form->createView()
        ]);
    }
}