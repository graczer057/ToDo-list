<?php

namespace App\Controller\Task;

use App\Form\TaskType;
use App\Repository\TodoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class EditController
 * @package App\Controller\Task
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
     * @param int $taskId
     * @param Request $request
     * @return Response
     * @throws \Exception
     * @Route("/{taskId}/edit", name="task_edit", methods={"GET", "POST"})
     */
    public function  EditTask(int $taskId, Request $request): Response{
        $task = $this->TodoRepository->findOneBy(['id' => $taskId]);
        if(is_null($task)){
            throw new \Exception("Task with id: {$taskId} is not found", 404);
        }
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->entityManager->persist($task);
            $this->entityManager->flush();

            $this->addFlash('success', 'task_edited');
            return $this->redirectToRoute('homepage');
        }

        return $this->render('form.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
