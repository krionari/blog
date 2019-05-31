<?php

namespace App\DataFixtures;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Article;
use Faker;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ArticleFixtures extends Fixture implements DependentFixtureInterface

{
    public function getDependencies()
    {
        return [CategoryFixtures::class];
    }

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        for($i=1; $i<51; $i++) {
            $article = new article();
            $article->setTitle(mb_strtolower($faker->sentence()));
            $article->setContent($faker->text(500));
            $manager->persist($article);
            $article->setCategory($this->getReference('categorie_' . rand(1,5)));
            $manager->flush();
        }
    }
}
