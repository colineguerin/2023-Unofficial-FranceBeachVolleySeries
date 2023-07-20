<?php

namespace App\DataFixtures;

use App\Entity\Article;
use DateTimeImmutable;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Factory;

class ArticleFixtures extends Fixture
{

    public const ARTICLES = [
        [
            'title' => 'Retour sur le dernier série 1 à Orléans',
            'subtitle' => 'Une superbe victoire pour la paire féminine de l\'île de Ré',
            'picture' => 'fbvs_orleans.jpg',
        ],
        [
            'title' => 'Le Pro Beach Tour bientôt à Paris',
            'subtitle' => 'Les meilleures paires du circuit mondial s\'affronteront à Paris en juillet',
            'picture' => 'fbvs_spikeblock.jpg',
        ],
        [
            'title' => 'La paire Cattet/Gauthier au sommet',
            'subtitle' => 'Les Lillois enchaînent les victoires sur le circuit national cette saison',
            'picture' => 'fbvs_teamhug.jpg',
        ],
        [
            'title' => 'Point d\'étape sur les séries 2 mixtes',
            'subtitle' => "Mis en place cette saison, les tournois mixtes officiels rencontrent un franc succès, malgré une participation inégale.",
            'picture' => 'fbvs_save.jpg',
        ],
        [
            'title' => 'Un nouveau club sur la côte Atlantique',
            'subtitle' => "Le Santa Brevina Beach Club compte déjà une cinquantaine d'adhérents",
            'picture' => 'fbvs_team.jpg',
        ],
        [
            'title' => 'Coupe de France : en route pour les phases finales',
            'subtitle' => "A l'issue du quatrième tour, voici les clubs qualifiés pour les phases finales de la Coupe de France 2023 !",
            'picture' => 'fbvs_shout.jpg',
        ],
        [
            'title' => 'Les arbitres officiels mobilisés cet été',
            'subtitle' => "Comme chaque année, la Fédération a besoin de ses arbitres, n'hésitez pas à passer l'examen.",
            'picture' => 'fbvs_referee.jpg',
        ],
        [
            'title' => 'Beau succès pour le Série 1 de Saintes',
            'subtitle' => "Après une phase de qualifications serrées, de superbes matchs ont eu lieu sur le terrain principal.",
            'picture' => 'fbvs_womanplayer.jpg',
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        foreach (self::ARTICLES as $key => $value) {
            $article = new Article();

            $article->setTitle($value['title']);
            $article->setSubtitle($value['subtitle']);
            $article->setContent($faker->paragraphs(5, true));
            $article->setPicture($value['picture']);
            $article->setCreatedAt(DateTimeImmutable::createFromMutable($faker
                ->dateTimeBetween('-6 months', '-3 weeks'))
            );
            $manager->persist($article);
        }
        $manager->flush();
    }
}