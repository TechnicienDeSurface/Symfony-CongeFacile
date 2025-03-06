<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Person;
use App\Entity\Department;
use App\Entity\Position;

class PersonFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // fixture 1
        $person = new Person();
        $person->setFirstName("John");
        $person->setLastName("Doe");
        $person->setManager(1);
        $person->setDepartment($this->getReference('Symfony', Department::class));
        $person->setPosition($this->getReference('Developpeur', Position::class));
        $person->setAlertNewRequest(true);
        $person->setAlertOnAnswer(false);
        $person->setAlertBeforeVacation(true);
        $manager->persist($person);
        $manager->flush();

        $this->addReference("John", $person);
    }
    public function getDependencies() : array
    {
        return [
            DepartmentFixtures::class,
            PositionFixtures::class
        ];
    }
}

