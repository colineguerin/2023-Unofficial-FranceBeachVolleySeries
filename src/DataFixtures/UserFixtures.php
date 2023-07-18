<?php

namespace App\DataFixtures;

use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $admin = new User();
        $admin->setPermitNumber('123456789');
        $admin->setFirstname('Coline');
        $admin->setLastname('Admin');
        $admin->setGender(0);
        $admin->setEmail('admin@fbvs.com');
        $admin->setPoint(0);
        $admin->setClub(
            $this->getReference('club_' . 'Bordeaux Beach Chillers')
        );
        $admin->setCreatedAt(DateTimeImmutable::createFromMutable($faker
            ->dateTimeBetween('-15 years', '-14 years')));
        $admin->setRoles(['ROLE_ADMIN']);
        $hashedPassword = $this->passwordHasher->hashPassword(
            $admin,
            'adminpassword'
        );
        $admin->setPassword($hashedPassword);
        $manager->persist($admin);

        for ($i = 0; $i < 10; $i++) {
            $woman = new User();
            $woman->setPermitNumber($faker->randomNumber(9, true));
            $woman->setEmail($faker->email);
            $woman->setPassword($this->passwordHasher->hashPassword(
                $woman,
                'userpassword'
            ));
            $woman->setRoles(['ROLE_USER']);
            $woman->setFirstName($faker->firstNameFemale);
            $woman->setLastName($faker->lastName);
            $woman->setGender(0);
            $woman->setPoint(0);
            $woman->setCreatedAt(DateTimeImmutable::createFromMutable($faker
                ->dateTimeBetween('-15 years', '-10 months')));
            $woman->setClub(
                $this->getReference('club_' . $faker->randomElement(
                    [
                        'Ré Beach Club',
                        'Bordeaux Beach Chillers',
                        'Montpellier Beach Volley',
                        'Sand System Association',
                        '59ers Beach-Volley',
                        'Beach Nantes Rezé',
                        'Anglet Beach Bask',
                        'Beach Volley Toulousain',
                        'Nice Beach Volley',
                        'Beach Sport Dijon'
                    ]
                    )));
            $manager->persist($woman);
        }

        for ($j = 0; $j < 10; $j++) {
            $man = new User();
            $man->setPermitNumber($faker->randomNumber(9, true));
            $man->setEmail($faker->email);
            $man->setPassword($this->passwordHasher->hashPassword(
                $man,
                'userpassword'
            ));
            $man->setRoles(['ROLE_USER']);
            $man->setFirstName($faker->firstNameMale);
            $man->setLastName($faker->lastName);
            $man->setGender(1);
            $man->setPoint(0);
            $man->setCreatedAt(DateTimeImmutable::createFromMutable($faker
                ->dateTimeBetween('-15 years', '-10 months')));
            $man->setClub(
                $this->getReference('club_' . $faker->randomElement(
                        [
                            'Ré Beach Club',
                            'Bordeaux Beach Chillers',
                            'Montpellier Beach Volley',
                            'Sand System Association',
                            '59ers Beach-Volley',
                            'Beach Nantes Rezé',
                            'Anglet Beach Bask',
                            'Beach Volley Toulousain',
                            'Nice Beach Volley',
                            'Beach Sport Dijon'
                        ]
                    )));
            $manager->persist($man);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ClubFixtures::class,
        ];
    }
}