<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly  UserPasswordHasherInterface $hasher
    ) {

    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setRoles(['ROLE_ADMIN'])
             ->setEmail('nadjia@nadjia.fr')
             ->setFirstName('nadjia')
             ->setLastName('oumaouche')
             ->setPassword($this->hasher->hashPassword($user, 'nadjianadjia'))
             ->isVerified();

        $user = new User();
        $user->setRoles(['ROLE_ADMIN'])
            ->setEmail('lynda@lynda.fr')
            ->setFirstName('lynda')
            ->setLastName('IHADADENE')
            ->setPassword($this->hasher->hashPassword($user, 'lyndalynda'))
            ->isVerified();

        $manager->persist($user);


        $faker = Factory::create('fr_FR');
        for ($i = 1; $i < 10; $i++) {
            $user = new User();
            $user->setRoles(['ROLE_USER'])
                ->setEmail($faker->email())
                ->setFirstName($faker->firstName())
                ->setLastName($faker->lastName())
                ->setPassword($this->hasher->hashPassword($user, '0000'))
                ->isVerified();
            $manager->persist($user);
            $this->addReference('user_' .$i, $user);
        }
        // $product = new Product();
        // $manager->persist($product);
        $manager->flush();
    }
}
