<?php

namespace App\Controller\Task;

use App\Entity\Todo;
use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CreateController
 * @package App\Controller\Task
 * @Route("/task")
 */

class CreateController extends AbstractController
{
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ){
        $this->entityManager = $entityManager;
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/new", name="todo_task_create", methods={"GET", "POST"})
     */
    public function CreateTask(Request $request): Response{
        $form = $this->createForm(TaskType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $formData = $form->getData();
            dump($formData);

            $newTodo = new Todo(
                $formData['description'],
                $formData['priority'],
                $formData['date']
            );

            $this->entityManager->persist($newTodo);
            $this->entityManager->flush();

            $this->addFlash('success', 'task_created');
            return $this->redirectToRoute('homepage');
        }

        return $this->render('form.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
