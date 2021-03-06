<?php

namespace App\Controller\Task;

use App\Repository\CategoryRepository;
use App\Repository\TodoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ListController
 * @package App\Controller\Task
 */

class ListController extends AbstractController
{
    private $TodoRepository;
    private $CategoryRepository;
    private $entityManager;

    public function __construct(
        TodoRepository $todoRepository,
        CategoryRepository $CategoryRepository,
        EntityManagerInterface $entityManager
    ){
        $this->TodoRepository = $todoRepository;
        $this->CategoryRepository = $CategoryRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @return Response
     * @Route("/", name="homepage", methods={"GET"})
     */
    public function HomePage(): ?Response
    {
        $todos = $this->TodoRepository->findBy([],[
            'priority' => 'DESC'
        ]);

        $date = new \DateTime("now");

        foreach($todos as $todo) {
            if (($todo->getDate()->getTimeStamp() < $date->getTimestamp()) || ($todo->getIsDone() == 1)) {
                $historicalTasks[] = $todo;
            } else {
                $actualTasks[] = $todo;
            }
        }

        return $this->render('homepage.html.twig',[
            'todos' => $todos,
            'historical' => $historicalTasks ?? null,
            'actual' => $actualTasks ?? null
        ]);
    }
}
