<?php

namespace App\Controller\landing;

use App\Repository\TodoRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ListController extends AbstractController
{
    private $TodoRepository;

    public function __construct(
        TodoRepository $todoRepository
    ){
        $this->TodoRepository = $todoRepository;
    }

    /**
     * @Route("", name="homepage", methods={"GET"})
     */
    public function HomePage()
    {
        $todos = $this->TodoRepository->findBy([],[
            'priority' => 'DESC'
        ]);

        $date = new DateTime("now");

        foreach($todos as $todo) {
            if (($todo->getDate()->getTimeStamp() < $date->getTimestamp()) || ($todo->getIsDone() == 1)) {
                $historicalTasks[] = $todo;
            } else {
                $actualTasks[] = $todo;
            }
        }

        return $this->render('landing/homepage.html.twig',[
            'todos' => $todos,
            'historical' => $historicalTasks ?? null,
            'actual' => $actualTasks ?? null
        ]);
    }
}
