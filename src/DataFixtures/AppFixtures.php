<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categories = array("projektowanie", "programowanie", "testowanie");
        $howMany = count($categories);

        for($i = 0; $i < $howMany; $i++){
            $category = new Category($categories[$i]);
            $manager->persist($category);
        }

        $manager->flush();
    }
}
