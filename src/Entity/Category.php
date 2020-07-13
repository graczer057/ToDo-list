<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 */
class Category
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Category;

    /**
     * @ORM\OneToMany(targetEntity=Todo::class, mappedBy="category")
     */
    private $tasks;


    public function __construct()
    {
        $this->tasks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategory(): ?string
    {
        return $this->Category;
    }

    public function setCategory(string $Category): self
    {
        $this->Category = $Category;

        return $this;
    }

    /**
     * @return Collection|Todo[]
     */
    public function getTasks(): Collection{
        return $this->tasks;
    }
    public function addTasks(Todo $todo): self{
        if(!$this->tasks->contains($todo)){
            $this->tasks[] = $todo;
            $todo->setCategory($this);
        }

        return $this;
    }

    public function removeTasks(Todo $todo): self{
        if($this->tasks->contains($todo)){
            $this->tasks->removeElement($todo);
            if($todo->getCategory() === $this){
                $todo->setCategory(null);
            }
        }

        return $this;
    }
}
