<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = faker\Factory::create('fr_FR');

        for ($i = 1; $i < 6; $i++) {
            $category = new Category();
            $category->setName($faker->slug);
            $manager->persist($category);
            $manager->flush();
            $this->addReference('categorie_' . $i, $category);
        }
        /* const CATEGORIES = [
             'PHP',
             'Javascript',
             'BDD',
             'Ruby',
             'C++'
             ];

         public function load(ObjectManager $manager)
         {
             foreach (self::CATEGORIES as $key => $categoryName){
                 $category = new Category();
                 $category->setName($categoryName);
                 $manager->persist($category);
                 $manager->flush();
                 $this->addReference('categorie_' . $key, $category);
             }

         } */
    }
}
