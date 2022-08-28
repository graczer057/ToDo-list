<?php

namespace App\Controller\tasks;

use App\Form\EditTaskType;
use App\Repository\TodoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/task")
 */
class EditController extends AbstractController
{
    private $entityManager;
    private $TodoRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        TodoRepository $TodoRepository
    ){
        $this->entityManager = $entityManager;
        $this->TodoRepository = $TodoRepository;
    }

    /**
     * @Route("/{taskId}/edit", name="task_edit", methods={"GET", "POST"})
     */
    public function  EditTask(int $taskId, Request $request): Response{
        $task = $this->TodoRepository->findOneBy(['id' => $taskId]);

        if(is_null($task)){
            throw new \Exception("Zadanie o numerze identyfikacyjnym: {$taskId} nie istnieje", 404);
        }

        $form = $this->createForm(EditTaskType::class, $task);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->entityManager->persist($task);
            $this->entityManager->flush();

            $this->addFlash('success', 'Zadanie zostało edytowane.');

            return $this->redirectToRoute('homepage');
        }

        return $this->render('tasks/editTask.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
