<?php

namespace App\DataFixtures;

use App\Entity\Article;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ArticleFixtures extends Fixture
{
    public const ARTICLES = [
        ['Spider-Man : Friend or Foe ?','Jonah Jamesson','article_1.jpg'],
        ['Green Goblin attacks Oscorp research labs','Peter Parker', 'article_2.jpeg'],
        ['Doctor Octopus holds up another bank !','Peter Parker', 'article_3.jpg'],
        ['A dangerous creature has been seen in the streets of New-York','Eddie Brock', 'article_4.jpg'],
        ['Another Spider-man ? Another criminal','Jonah Jamesson', 'article_5.jpg']
    ];
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        foreach (self::ARTICLES as $articleItem) {
            $article = new Article();
            $article->setTitle($articleItem[0]);
            $article->setAuthor($articleItem[1]);
            $article->setDate($faker->dateTimeBetween('-3 months', '-2 days'));
            $article->setPicture($articleItem[2]);
            $uploadDir = __DIR__ . '/../../public/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir);
            }
            copy(
                __DIR__ . '/data/' . $articleItem[2],
                $uploadDir . '/' . $articleItem[2]
            );
            $article->setSummary($faker->paragraph(7));
            $article->setContent(implode("", array_map(function ($paragraph) {
                return "<p>" . $paragraph . "</p>";
            }, $faker->paragraphs($faker->numberBetween(15, 30)))));
            $manager->persist($article);
        }

        $manager->flush();
    }
}
