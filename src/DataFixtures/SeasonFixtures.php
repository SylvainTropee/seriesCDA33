<?php

namespace App\DataFixtures;

use App\Entity\Season;
use App\Entity\Serie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $series = $manager->getRepository(Serie::class)->findAll();

        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 50; $i++) {

            $season = new Season();
            $season
                ->setDateCreated($faker->dateTimeBetween('- 6 month'))
                ->setPoster('poster.png')
                ->setTmdbId($faker->randomDigit())
                ->setFirstAirDate($faker->dateTimeBetween('- 6 month'))
                ->setNumber($faker->numberBetween(1, 8))
                //->setSerie($this->getReference("serie-" . $faker->numberBetween(1,10)));
                ->setSerie($faker->randomElement($series));

            $manager->persist($season);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            SerieFixtures::class
        ];
    }
}






