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
        $manager->flush();

        // Fixture 2
    }
    public function getDependencies()
    {
        return [];
    }
}