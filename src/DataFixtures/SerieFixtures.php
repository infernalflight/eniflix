<?php

namespace App\DataFixtures;

use App\Entity\Serie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class SerieFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 1000; $i++) {
            $serie = new Serie();
            $serie->setName($faker->realText(30))
                ->setOverview($faker->paragraph(2))
                ->setGenre($faker->randomElement(['Drama', 'Western', 'Comedy', 'Horror', 'Thriller']))
                ->setStatus($faker->randomElement(['Returning', 'Ended', 'Canceled']))
                ->setVote($faker->randomFloat(2, 0, 10))
                ->setPopularity($faker->randomFloat(2, 200, 900))
                ->setFirstAirDate($faker->dateTimeBetween('-10 year', '-1 month'))
                ->setDateCreated(new \DateTime());

            if ($serie->getStatus() !== 'Returning') {
                $serie->setLastAirDate($faker->dateTimeBetween($serie->getFirstAirDate(), '-1 day'));
            }
            $manager->persist($serie);
        }
        $manager->flush();
    }
}
