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
        // Fixture 1
        $person1 = new Person();
        $person1->setFirstName("John");
        $person1->setLastName("Doe");
        $person1->setManager(1);
        $person1->setDepartment($this->getReference('Symfony', Department::class));
        $person1->setPosition($this->getReference('Developpeur', Position::class));
        $person1->setAlertNewRequest(true);
        $person1->setAlertOnAnswer(false);
        $person1->setAlertBeforeVacation(true);
        $manager->persist($person1);
        $this->addReference("John", $person1);

        // Fixture 2
        $person2 = new Person();
        $person2->setFirstName("Jane");
        $person2->setLastName("Smith");
        $person2->setManager(2);
        $person2->setDepartment($this->getReference('Marketing', Department::class));
        $person2->setPosition($this->getReference('Manager', Position::class));
        $person2->setAlertNewRequest(false);
        $person2->setAlertOnAnswer(true);
        $person2->setAlertBeforeVacation(false);
        $manager->persist($person2);
        $this->addReference("Jane", $person2);

        // Fixture 3
        $person3 = new Person();
        $person3->setFirstName("Alice");
        $person3->setLastName("Johnson");
        $person3->setManager(3);
        $person3->setDepartment($this->getReference('RH', Department::class));
        $person3->setPosition($this->getReference('Recruteur', Position::class));
        $person3->setAlertNewRequest(true);
        $person3->setAlertOnAnswer(true);
        $person3->setAlertBeforeVacation(true);
        $manager->persist($person3);
        $this->addReference("Alice", $person3);

        // Fixture 4
        $person4 = new Person();
        $person4->setFirstName("Bob");
        $person4->setLastName("Brown");
        $person4->setManager(4);
        $person4->setDepartment($this->getReference('CMS', Department::class));
        $person4->setPosition($this->getReference('Developpeur', Position::class));
        $person4->setAlertNewRequest(false);
        $person4->setAlertOnAnswer(false);
        $person4->setAlertBeforeVacation(true);
        $manager->persist($person4);
        $this->addReference("Bob", $person4);

        // Fixture 5
        $person5 = new Person();
        $person5->setFirstName("Charlie");
        $person5->setLastName("Davis");
        $person5->setManager(5);
        $person5->setDepartment($this->getReference('BU Design', Department::class));
        $person5->setPosition($this->getReference('Graphiste', Position::class));
        $person5->setAlertNewRequest(true);
        $person5->setAlertOnAnswer(false);
        $person5->setAlertBeforeVacation(false);
        $manager->persist($person5);
        $this->addReference("Charlie", $person5);

        $manager->flush();
    }
    public function getDependencies() : array
    {
        return [
            DepartmentFixtures::class,
            PositionFixtures::class
        ];
    }
}

