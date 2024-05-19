<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $user = new User();
        $user->setEmail('dorje.curreli@cobaltopoetry.art');
        $user->setPassword('$2y$13$PaySfGwB02nUkHNLPhwbHeeTiRasafz8gcmok5xcTLJ8Rl9TzLOIO'); // secret
        $user->setRoles(['ROLE_USER']);

        $manager->persist($user);


        $faker = Factory::create();

        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $user->setEmail($faker->email);
            $user->setPassword(password_hash('secret', PASSWORD_BCRYPT));

            $manager->persist($user);

            $manager->flush();
        }

    }
}
