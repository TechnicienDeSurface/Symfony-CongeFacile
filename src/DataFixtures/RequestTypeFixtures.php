<?php

namespace App\DataFixtures;

use App\Entity\RequestType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RequestTypeFixtures extends Fixture {
    public function load(ObjectManager $manager): void {
        $requestType = new RequestType();
        $requestType->setName("Collaborateur");
        $this->addReference("Collaborateur", $requestType);
        $manager->persist($requestType);
        $manager->flush();
    }
    public function getDependencies()
    {
        return [];
    }
}