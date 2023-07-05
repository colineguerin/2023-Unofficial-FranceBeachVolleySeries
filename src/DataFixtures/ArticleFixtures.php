<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ArticleFixtures extends Fixture
{

    public const ARTICLES = [
        [
            'title' => 'Retour sur le dernier série 1 à Orléans',
            'subtitle' => 'Une superbe victoire pour la paire féminine de l\'île de Ré',
            'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
            'picture' => 'fbvs_orleans.jpg',
        ],
        [
            'title' => 'Le Pro Beach Tour bientôt à Paris',
            'subtitle' => 'Les meilleures paires du circuit mondial s\'affronteront à Paris en juillet',
            'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
            'picture' => 'fbvs_spikeblock.jpg',
        ],
        [
            'title' => 'La paire Cattet/Gauthier au sommet',
            'subtitle' => 'Les Lillois enchaînent les victoires sur le circuit national cette saison',
            'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
            'picture' => 'fbvs_teamhug.jpg',
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::ARTICLES as $key => $value) {
            $article = new Article();
            $now = new \DateTimeImmutable();

            $article->setTitle($value['title']);
            $article->setSubtitle($value['subtitle']);
            $article->setContent($value['content']);
            $article->setPicture($value['picture']);
            $article->setCreatedAt($now);
            $manager->persist($article);
        }
        $manager->flush();
    }
}