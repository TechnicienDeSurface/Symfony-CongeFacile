<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use App\Entity\Person;

class UserFixtures extends Fixture implements DependentFixtureInterface{
    public function load(ObjectManager $manager): void {
        // fixture 1
        $user = new User();
        $user->setEmail("Manager@manager.com");
        $user->setPassword("test");
        $user->setEnabled(true);
        $user->setRole("Manager");
        $user->setPerson($this->getReference("John", Person::class));
        $user->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($user);
        $manager->flush();

        $this->addReference('Manager@manager.com', $user);

        // fixture 2
        $user2 = new User();
        $user2->setEmail("colab@colab.com");
        $user2->setPassword("test");
        $user2->setEnabled(true);
        $user2->setRole("Collaborateur");
        $user2->setPerson($this->getReference("Jane", Person::class));
        $user2->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($user2);
        $manager->flush();

        $this->addReference('colab@colab.com', $user2);
    }
    public function getDependencies(): array
    {
        return [
            PersonFixtures::class,
        ];
    }
}