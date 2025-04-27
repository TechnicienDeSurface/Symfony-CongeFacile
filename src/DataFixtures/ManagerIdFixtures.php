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
        $personRepo = $manager->getRepository(Person::class);
        $userRepo = $manager->getRepository(User::class);

        $person = $personRepo->find(1);
        $managerUser = $userRepo->find(5);

        $person->setManager($managerUser);
        $manager->flush();

        $person = $personRepo->find(3);
        $managerUser = $userRepo->find(5);

        $person->setManager($managerUser);
        $manager->flush();

        $person = $personRepo->find(4);
        $managerUser = $userRepo->find(5);

        $person->setManager($managerUser);
        $manager->flush();

        $person = $personRepo->find(5);
        $managerUser = $userRepo->find(5);

        $person->setManager($managerUser);
        $manager->flush();

        $person = $personRepo->find(6);
        $managerUser = $userRepo->find(5);

        $person->setManager($managerUser);
        $manager->flush();
    }
    
    public function getDependencies() : array
    {
        return [
            UserFixtures::class,
        ];
    }
}


