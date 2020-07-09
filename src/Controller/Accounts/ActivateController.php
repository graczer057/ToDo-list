<?php

namespace App\Controller\Accounts;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;


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

    public function activate(string $token): Response{

        $date = new \DateTime("now");
        $user = $this->UserRepository->findOneBy(['token' => $token]);
        if(is_null($user)){
            return $this->render('base.html.twig');
        }
        if($user->getTokenExpire()->getTimestamp() > $date->getTimestamp()){
            $user -> setIsActiveUser('1');
            $user -> setToken(null);
            $user -> setDate(null);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }else{
            return $this->render('expire');
        }
        return $this->redirect('base.html.twig');
    }
}
