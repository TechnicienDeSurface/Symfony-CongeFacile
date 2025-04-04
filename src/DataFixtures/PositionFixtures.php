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

        $position2 = new Position();
        $position2->setName("Manager");
        $manager->persist($position2);
        $this->addReference("Manager", $position2);

        $position3 = new Position();
        $position3->setName("Graphiste");
        $manager->persist($position3);
        $this->addReference("Graphiste", $position3);

        $position4 = new Position();
        $position4->setName("Analyste");
        $manager->persist($position4);
        $this->addReference("Analyste", $position4);

        $position5 = new Position();
        $position5->setName("Recruteur");
        $manager->persist($position5);
        $this->addReference("Recruteur", $position5);

        $position6 = new Position();
        $position6->setName("Chef de projet");
        $manager->persist($position6);
        $this->addReference("Chef de projet", $position6);

        $position7 = new Position();
        $position7->setName("Directeur");
        $manager->persist($position7);
        $this->addReference("Directeur", $position7);


        $manager->flush();
    }

}