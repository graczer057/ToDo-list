<?php
namespace App\Controller\Accounts;

use App\Form\ExpireFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\RegistrationFormType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;
use App\Security\Authenticator;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Mime\Email;

class ExpireController extends AbstractController
{
    private $entityManger;
    private $UserRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserRepository $UserRepository
    ){
        $this->entityManger = $entityManager;
        $this->UserRepository = $UserRepository;
    }
    /**
     * @param Request $request
     * @return Response
     * @Route("/expire}", name="expire")
     */
    public function expire(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ExpireFormType::class);
        $form->handleRequest($request);

        $formData = $form->getData();

        $this->createNotFoundException();

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->UserRepository->findOneBy(['email' => $formData['email']]);
            $date = new \DateTime("now");
            $date->modify('+15 minutes');
            $token = md5(uniqid(time()));
            $user->setToken($token);
            $user->setDate($date);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $url = $this->generateUrl('app_activate_active', array('token' => $user->getToken()), UrlGenerator::ABSOLUTE_URL);

            $email = (new Email())
                ->from('bartlomiej.szyszkowski@yellows.eu')
                ->to($user->getEmail())
                ->subject('Activate your account')
                ->html($url);

            $mailer->send($email);
            $this->addFlash('success', 'task_created');
            return $this->redirectToRoute('homepage');
        }
        return $this->render('expire.html.twig', [
            'form' => $form->createView()
        ]);
    }
}