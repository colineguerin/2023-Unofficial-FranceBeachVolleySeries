<?php

namespace App\DataFixtures;

use App\Entity\Tournament;
use DateTimeImmutable;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Factory;

class TournamentFixtures extends Fixture
{
    public const TOURNAMENTS = [
        [
          'name' => 'Chill Beach Tour #1',
          'category' => 'Série 3 Masculin - 150',
          'location' => 'Bordeaux',
            'club' => 'Bordeaux Beach Chillers'
        ],
        [
            'name' => 'Chill Beach Tour #1',
            'category' => 'Série 3 Féminin - 150',
            'location' => 'Bordeaux',
            'club' => 'Bordeaux Beach Chillers'
        ],
        [
            'name' => 'Ré Beach Open',
            'category' => 'Série 1 Féminin - 1500',
            'location' => 'Ile de Ré',
            'club' => 'Ré Beach Club'
        ],
        [
            'name' => 'Nice Pro Beach Tour',
            'category' => 'Série 2 Masculin - 1000',
            'location' => 'Nice',
            'club' => 'Nice Beach Volley'
        ],
        [
            'name' => 'Nice Pro Beach Tour',
            'category' => 'Série 2 Féminin - 1000',
            'location' => 'Nice',
            'club' => 'Nice Beach Volley'
        ],
        [
            'name' => 'Mixte de Dijon #2',
            'category' => 'Série 3 Mixte - 150',
            'location' => 'Dijon',
            'club' => 'Beach Sport Dijon'
        ],
        [
            'name' => 'Santa Brevina Beach Series #4',
            'category' => 'Série 3 Mixte - 150',
            'location' => 'Saint-Brévin',
            'club' => 'Beach Nantes Rezé'
        ],
        [
            'name' => 'Open d\'Orléans',
            'category' => 'Série 1 Féminin - 1500',
            'location' => 'Orléans',
            'club' => 'Sand System Association'
        ],
        [
            'name' => 'Open d\'Orléans',
            'category' => 'Série 1 Masculin - 1500',
            'location' => 'Orléans',
            'club' => 'Sand System Association'
        ],
        [
            'name' => 'Open Beach Volley Anglet',
            'category' => 'Série 2 Féminin - 750',
            'location' => 'Anglet',
            'club' => 'Anglet Beach Bask'
        ],
        [
            'name' => 'Open Beach Volley Anglet',
            'category' => 'Série 2 Masculin - 750',
            'location' => 'Anglet',
            'club' => 'Anglet Beach Bask'
        ],
        [
            'name' => 'Montpellier Beach Tour',
            'category' => 'Série 2 Masculin - 500',
            'location' => 'Montpellier',
            'club' => 'Montpellier Beach Volley'
        ],
        [
            'name' => 'Montpellier Beach Tour',
            'category' => 'Série 2 Masculin - 500',
            'location' => 'Montpellier',
            'club' => 'Montpellier Beach Volley'
        ],
        [
            'name' => 'Green Beach Open',
            'category' => 'Série 3 Mixte - 150',
            'location' => 'Lille',
            'club' => '59ers Beach-Volley'
        ],
        [
            'name' => 'Sand System Beach Open Mixte #3',
            'category' => 'Série 3 Mixte - 150',
            'location' => 'Paris',
            'club' => 'Sand System Association'
        ],
    ];

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        foreach (self::TOURNAMENTS as $value) {
            $tournament = new Tournament();
            $tournament->setName($value['name']);
            $tournament->setCategory($value['category']);
            $tournament->setLocation($value['location']);
            $tournament->setMaxTeam($faker->numberBetween(8, 24));
            $tournament->setDetails($faker->text());
            $tournament->setTournamentDate(DateTimeImmutable::createFromMutable($faker
                ->dateTimeBetween('-3 months', '+3 months'))
            );
            $tournament->setCreatedAt(DateTimeImmutable::createFromMutable($faker
                ->dateTimeBetween('-6 months', '-3 weeks'))
            );
            $tournament->setClub(
                $this->getReference('club_' . $value['club'])
            );
            $manager->persist($tournament);
        }

        $manager->flush();
    }
}