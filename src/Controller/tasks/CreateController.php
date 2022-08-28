<?php

namespace App\Controller\tasks;

use App\Entity\Todo;
use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
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
     * @Route("/new", name="todo_task_create", methods={"GET", "POST"})
     */
    public function CreateTask(Request $request){
        $form = $this->createForm(TaskType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $formData = $form->getData();

            $newTodo = new Todo(
                $formData['category'],
                $formData['description'],
                $formData['priority'],
                $formData['date'],
                false
            );

            $this->entityManager->persist($newTodo);
            $this->entityManager->flush();

            $this->addFlash('success', 'Zadanie zostało pomyślnie utworzone');

            return $this->redirectToRoute('homepage');
        }

        return $this->render('tasks/addTask.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
