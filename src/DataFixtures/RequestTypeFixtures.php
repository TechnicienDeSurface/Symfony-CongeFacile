<?php

namespace App\DataFixtures;

use App\Entity\RequestType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RequestTypeFixtures extends Fixture {
    public function load(ObjectManager $manager): void {
        $requestType1 = new RequestType();
        $requestType1->setName("Congé");
        $this->addReference("Congé", $requestType1);
        $manager->persist($requestType1);

        $requestType2 = new RequestType();
        $requestType2->setName("Congé sans soldes");
        $this->addReference("Congé sans soldes", $requestType2);
        $manager->persist($requestType2);

        $requestType3 = new RequestType();
        $requestType3->setName("RTT");
        $this->addReference("RTT", $requestType3);
        $manager->persist($requestType3);

        $requestType4 = new RequestType();
        $requestType4->setName("Congé maternité");
        $this->addReference("Congé maternité", $requestType4);
        $manager->persist($requestType4);

        $requestType5 = new RequestType();
        $requestType5->setName("Congé paternité");
        $this->addReference("Congé paternité", $requestType5);
        $manager->persist($requestType5);

        $requestType6 = new RequestType();
        $requestType6->setName("Congé maladie");
        $this->addReference("Congé maladie", $requestType6);
        $manager->persist($requestType6);

        $manager->flush();
    }
    public function getDependencies()
    {
        return [];
    }
}