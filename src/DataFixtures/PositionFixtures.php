<?php

namespace App\DataFixtures;

use App\Entity\Position;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PositionFixtures extends Fixture {
    public function load(ObjectManager $manager): void {
        // fixture 1
        $position1 = new Position();
        $position1->setName("Developpeur");
        $manager->persist($position1);
        $this->addReference("Developpeur", $position1);
        $manager->flush();
    }

}