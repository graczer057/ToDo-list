<?php
namespace App\Controller\invitations;

use App\Form\InviteType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGenerator;

class InviteController extends AbstractController
{
    private $UserRepository;

    public function __construct(
        UserRepository $UserRepository
    ){
        $this->UserRepository = $UserRepository;
    }

    /**
     * @Route("/invite}", name="invite")
     */
    public function invite(Request $request, MailerInterface $mailer)
    {
        $form = $this->createForm(InviteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            $user = $this->UserRepository->findOneBy(['email' => $formData['email']]);

            if($user){
                $url = $this->generateUrl('app_register', array(), UrlGenerator::ABSOLUTE_URL);

                $email = (new Email())
                    ->from('bartlomiej.szyszkowski@yellows.eu')
                    ->to($formData['email'])
                    ->subject('Zaproszenie od twojego znajomego')
                    ->html($url);

                $mailer->send($email);

                $this->addFlash('success', 'Zaproszenie do twojego znajomego zostało pomyślnie wysłane.');

                return $this->redirectToRoute('homepage');
            }else{
                $this->addFlash('error', 'Przepraszamy, ale użytkownik o podanym adresie email już istnieje.');

                return $this->redirectToRoute('homepage');
            }
        }
        return $this->render('invitations/invite.html.twig', [
            'form' => $form->createView()
        ]);
    }
}