<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class DefaultFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i=0 ; $i < 50 ; $i++) {
       $categorie = new Category();
       $categorie->setCode('Code00'.$i);
       $categorie->setTitle(uniqid().$faker->text(5));
       $manager->persist($categorie);

//
       $article = new Article();
       $article->setRef('Ref00'.$i);
       $article->setDescription($faker->text(5));
       $manager->persist($article);



   }

        $manager->flush();
    }
}
