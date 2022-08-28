<?php
namespace App\Controller\security;

use App\Form\ExpireFormType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGenerator;

class ExpireController extends AbstractController
{
    private $UserRepository;

    public function __construct(
        UserRepository $UserRepository
    ){
        $this->UserRepository = $UserRepository;
    }

    /**
     * @Route("/expire}", name="expire")
     */
    public function expire(Request $request, MailerInterface $mailer)
    {
        $form = $this->createForm(ExpireFormType::class);
        $form->handleRequest($request);

        $formData = $form->getData();

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->UserRepository->findOneBy(['email' => $formData['email']]);

            if($user){
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
                    ->subject('Aktywuj swoje konto.')
                    ->html($url);

                $mailer->send($email);

                $this->addFlash('success', 'Nowy link został wysłany');

                return $this->redirectToRoute('homepage');
            }else{
                $this->addFlash('error', 'Przykro nam, ale konto o podanym adresie email nie istnieje.');

                return $this->render('security/Expire.html.twig', [
                    'form' => $form->createView()
                ]);
            }
        }
        return $this->render('security/Expire.html.twig', [
            'form' => $form->createView()
        ]);
    }
}