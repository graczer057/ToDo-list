<?php

namespace App\Controller\tasks;

use App\Repository\TodoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/task")
 */
class DeleteController extends AbstractController
{
    private $entityManager;
    private $TodoRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        TodoRepository $todoRepository
    ){
        $this->entityManager = $entityManager;
        $this->TodoRepository = $todoRepository;
    }

    /**
     * @Route("/{taskId}}/remove", name="task_remove", methods={"DELETE"})
     */
    public function RemoveTask(int $taskId){
        $task = $this->TodoRepository->findOneBy(['id' => $taskId]);

        if(is_null($task)){
            throw new \Exception("Zadanie z numerem identyfikującym: {$taskId} nie zostało znalezione", 404);
        }

        $this->entityManager->remove($task);
        $this->entityManager->flush();

        $this->addFlash('success', 'Zadanie zostało usunięte');

        return $this->redirectToRoute('homepage');
    }
}
