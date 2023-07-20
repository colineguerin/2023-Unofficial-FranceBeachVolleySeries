<?php

namespace App\DataFixtures;

use App\Entity\Club;
use DateTimeImmutable;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Factory;

class ClubFixtures extends Fixture
{
    public const CLUBS = [
        [
            'name' => 'Ré Beach Club'
        ],
        [
            'name' => 'Bordeaux Beach Chillers'
        ],
        [
            'name' => 'Montpellier Beach Volley'
        ],
        [
            'name' => 'Sand System Association'
        ],
        [
            'name' => '59ers Beach-Volley'
        ],
        [
            'name' => 'Beach Nantes Rezé'
        ],
        [
            'name' => 'Anglet Beach Bask'
        ],
        [
            'name' => 'Beach Volley Toulousain'
        ],
        [
            'name' => 'Nice Beach Volley'
        ],
        [
            'name' => 'Beach Sport Dijon'
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        foreach (self::CLUBS as $key => $value)
        {
            $club = new Club();
            $club->setName($value['name']);
            $club->setCreatedAt(DateTimeImmutable::createFromMutable($faker
                ->dateTimeBetween('-15 years', '-2 years')));
            $manager->persist($club);
            $this->addReference('club_' . $value['name'], $club);
        }
        $manager->flush();
    }
}