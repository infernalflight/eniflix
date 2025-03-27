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

        for ($i = 0; $i < 500; $i++) {
            $serie = new Serie();
            $serie->setName($faker->words(5, true))
                ->setOverview($faker->paragraphs(1, true))
                ->setGenres($faker->randomElement(['Drama', 'Comedy', 'Thriller', 'SF', 'Gore']))
                ->setStatus($faker->randomElement(['returning', 'ended', 'canceled']))
                ->setVote($faker->randomFloat(2, 0, 10))
                ->setPopularity($faker->randomFloat(2, 200, 900))
                ->setFirstAirDate($faker->dateTimeBetween('-10 year', '-1 month'))
                ->setDateCreated(new \DateTime());

            if ($serie->getStatus() !== 'returning') {
                $serie->setLastAirDate($faker->dateTimeBetween($serie->getFirstAirDate()));
            }

            $manager->persist($serie);
        }

        $manager->flush();
    }
}
