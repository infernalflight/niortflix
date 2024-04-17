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

        for ($i = 0; $i<1000; $i++) {
            $serie = new Serie();
            $serie->setName($faker->words(4, true));
            $serie->setOverview($faker->words(50, true));
            $serie->setFirstAirDate($faker->dateTimeBetween(new \DateTime('-10 year'), new \DateTime('-10 day')));
            $serie->setPopularity($faker->randomFloat(2, 0.0, 1000.0));
            $serie->setVote($faker->randomFloat(2, 0.0, 10.0));
            $serie->setStatus($faker->randomElement(['Returning', 'Ended', 'Canceled']));
            $serie->setGenres($faker->randomElement(['Action', 'Animé', 'Thriller', 'Drama', 'SF', 'Comedy']));
            $serie->setDateCreated(new \DateTime());

            if (in_array($serie->getStatus(), ['Canceled', 'Ended'])) {
                $serie->setLastAirDate($faker->dateTimeBetween($serie->getFirstAirDate(), new \DateTime()));
            }

            $manager->persist($serie);
        }

        $manager->flush();

    }
}
