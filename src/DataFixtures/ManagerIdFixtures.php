<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Person;
use App\Entity\User;

class ManagerIdFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $personRepo = $manager->getRepository(Person::class);
        $userRepo = $manager->getRepository(User::class);

        // Récupération d'une personne et d'un utilisateur existants
        $person = $personRepo->find(11, 12); // ID de la personne à mettre à jour
        $managerUser = $userRepo->find(15); // ID du manager (User)

        if ($person && $managerUser) {
            $person->setManager($managerUser);
            $manager->flush();
        } else {
            throw new \Exception("Personne (ID 11) ou manager (User ID 15) introuvable !");
        }
    }
}


