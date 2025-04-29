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

        // Fixture 6
        $department6 = new Department();
        $department6->setName("Comptabilité");
        $this->addReference("Comptabilité", $department6);
        $manager->persist($department6);
        $manager->flush();

        // Fixtures 7
        $department7 = new Department();
        $department7->setName("Service commerciale");
        $this->addReference("Commerciale", $department7);
        $manager->persist($department7);
        $manager->flush();

        // Fixture 8
        $department8 = new Department();
        $department8->setName("Réseau");
        $this->addReference('Réseau', $department8);
        $manager->persist($department8);

        $manager->flush();

    }
    public function getDependencies()
    {
        return [];
    }
}