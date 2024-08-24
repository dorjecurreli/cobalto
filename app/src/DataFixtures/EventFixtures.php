<?php

namespace App\DataFixtures;

use App\Entity\Event;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class EventFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 20; $i++) {
            $event = new Event();
            $event->setName($faker->word() . ' Event');
            $event->setStartDate(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('now', '+1 year')));
            $event->setStartTime(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('now', '+1 year')));
            $event->setDescription($faker->text());

            $manager->persist($event);

            $manager->flush();
        }

    }
}
