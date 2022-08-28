<?php
namespace App\Controller\security;

use App\Form\SendFormType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGenerator;

class SendController extends AbstractController
{
    private $UserRepository;

    public function __construct(
        UserRepository $UserRepository
    ){
        $this->UserRepository = $UserRepository;
    }

    /**
     * @Route("/email}", name="email_password_change")
     */
    public function expire(Request $request, MailerInterface $mailer)
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
                ->subject('Zmień swoje hasło.')
                ->html($url);

            $mailer->send($email);

            $this->addFlash('success', 'Wysłaliśmy Tobie link do zmiany hasła.');

            return $this->redirectToRoute('homepage');
        }

        return $this->render('security/send.html.twig', [
            'form' => $form->createView()
        ]);
    }
}