<?php

namespace App\DataFixtures;

use App\Entity\Department;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DepartmentFixtures extends Fixture {
    public function load(ObjectManager $manager): void {
        // Fixture 1
        $department = new Department();
        $department->setName("Symfony");
        $this->addReference('Symfony', $department);
        $manager->persist($department);

        $department2 = new Department();
        $department2->setName("CMS");
        $this->addReference('CMS', $department2);
        $manager->persist($department2);

        // Fixture 3
        $department3 = new Department();
        $department3->setName("BU Design");
        $this->addReference('BU Design', $department3);
        $manager->persist($department3);

        // Fixture 4
        $department4 = new Department();
        $department4->setName("Marketing");
        $this->addReference('Marketing', $department4);
        $manager->persist($department4);

        // Fixture 5
        $department5 = new Department();
        $department5->setName("RH");
        $this->addReference('RH', $department5);
        $manager->persist($department5);

        $manager->flush();

        // Fixture 2
    }
    public function getDependencies()
    {
        return [];
    }
}