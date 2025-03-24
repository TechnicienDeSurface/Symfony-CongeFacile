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

        $people = [
            ['name' => 'Alice', 'email' => 'alice@example.com', 'role' => ['ROLE_COLLABORATEUR'], 'password' => 'alice123'],
            ['name' => 'Bob', 'email' => 'bob@example.com', 'role' => ['ROLE_COLLABORATEUR'], 'password' => 'bob123'],
            ['name' => 'Charlie', 'email' => 'charlie@example.com', 'role' => ['ROLE_COLLABORATEUR'], 'password' => 'charlie123'],
            ['name' => 'John', 'email' => 'John@example.com', 'role' => ['ROLE_COLLABORATEUR'], 'password' => 'john123'],
            ['name' => 'Jane', 'email' => 'Jane@example.com', 'role' => ['ROLE_MANAGER'], 'password' => 'Jane123']
        ];

        foreach ($people as $personData) {
            $user = new User();
            $user->setEmail($personData['email']);
            $plaintextPassword = $personData['password'];

            // Hash the password
            $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $plaintextPassword
            );
            $user->setPassword($hashedPassword);

            $user->setEnabled(true);
            $user->setRoles($personData['role']);
            $user->setPerson($this->getReference($personData['name'], Person::class));
            $user->setCreatedAt(new \DateTimeImmutable());
            $manager->persist($user);

            $this->addReference($personData['email'], $user);

            $manager->flush();
        }
    }
    public function getDependencies(): array
    {
        return [
            PersonFixtures::class,
        ];
    }
}