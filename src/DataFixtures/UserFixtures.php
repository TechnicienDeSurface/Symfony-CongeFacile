<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use App\Entity\Person;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface{
    public function __construct(private UserPasswordHasherInterface $passwordHasher){
        
    }
    public function load(ObjectManager $manager): void {
        // fixture 1
        $user = new User();
        $user->setEmail("Manager@manager.com");
         // ... e.g. get the user data from a registration form
         $plaintextPassword = "test" ; 
 
         // hash the password (based on the security.yaml config for the $user class)
         $hashedPassword = $this->passwordHasher->hashPassword(
             $user,
             $plaintextPassword
         );
         $user->setPassword($hashedPassword);
 
         // ...
        $user->setEnabled(true);
        $user->setRoles(["ROLE_MANAGER"]);
        $user->setPerson($this->getReference("John", Person::class));
        $user->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($user);

        $this->addReference('Manager@manager.com', $user);

        // fixture 2
        $user2 = new User();
        $user2->setEmail("colab@colab.com");
         // ... e.g. get the user data from a registration form
         $plaintextPassword = "test" ; 
 
         // hash the password (based on the security.yaml config for the $user class)
         $hashedPassword = $this->passwordHasher->hashPassword(
             $user2,
             $plaintextPassword
         );
         $user2->setPassword($hashedPassword);
 
        $user2->setEnabled(true);
        $user2->setRoles(["ROLE_COLLABORATEUR"]);
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