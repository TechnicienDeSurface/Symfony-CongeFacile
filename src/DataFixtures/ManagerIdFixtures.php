<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Person;
use App\Entity\User;

class ManagerIdFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $managerUser = $this->getReference('Jane@example.com', User::class);

        $personNames = ['Alice', 'Bob', 'Charlie', 'Martin', 'John'];

        foreach ($personNames as $name) {
            $person = $this->getReference($name, Person::class);
            $person->setManager($managerUser);
            $manager->persist($person);
        }

        $manager->flush(); 
    }
    
    public function getDependencies() : array
    {
        return [
            UserFixtures::class,
        ];
    }
}


