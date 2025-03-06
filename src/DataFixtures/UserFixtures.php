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
        $user->setEmail("admin@admin.com");
        $user->setPassword("test");
        $user->setEnabled(true);
        $user->setRole("Manager");
        $user->setPerson($this->getReference("John", Person::class));
        $user->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($user);
        $manager->flush();

        $this->addReference('admin@admin.com', $user);
    }
    public function getDependencies(): array
    {
        return [
            PersonFixtures::class,
        ];
    }
}