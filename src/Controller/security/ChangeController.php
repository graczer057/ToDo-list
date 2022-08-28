<?php
namespace App\Controller\security;

use App\Form\ChangeFormType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ChangeController extends AbstractController
{
    private $UserRepository;

    public function __construct(
        UserRepository $UserRepository
    ){
        $this->UserRepository = $UserRepository;
    }

    /**
     * @Route("/change/{token}", name="change_password", methods={"GET", "POST"})
     */
    public function change(string $token, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $form = $this->createForm(ChangeFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $this->UserRepository->findOneBy(['token' => $token]);

            if (is_null($user)) {
                throw new \Exception("Użytkownik z podanym numerem identyfikującym: {$token} nie istnieje", 404);
            }

            $date = new \DateTime("now");

            if ($user->getTokenExpire()->getTimestamp() > $date->getTimestamp()) {
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('repeatPassword')->getData()
                    ));
            } else {
                $user->setToken(null);
                $user->setDate(null);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('error', 'Link wygasł. W celu przypomnienia hasła prosimy o ponowne rozpoczęcie procedury.');

                return $this->render('security/login.html.twig', [
                    'form' => $form->createView()
                ]);
            }

            $user->setToken(null);
            $user->setDate(null);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Hasło zostało pomyślnie zmienione.');

            return $this->redirectToRoute('homepage');
        }
        return $this->render('security/change.html.twig', [
            'form' => $form->createView()
        ]);
    }
}