<?php

namespace App\DataFixtures;

use App\Entity\Request;
use App\Entity\Department;
use App\Entity\RequestType;
use App\Entity\Person;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RequestFixtures extends Fixture implements DependentFixtureInterface {
    public function load(ObjectManager $manager): void {
        $request = new Request();
        $request->setRequestType($this->getReference("Congé sans soldes", RequestType::class));
        $request->setCollaborator($this->getReference('John', Person::class));
        $request->setDepartment($this->getReference('Symfony', Department::class));
        $request->setCreatedAtValue();
        $request->setStartAt(new \DateTime("2025-05-01"));
        $request->setEndAt(new \DateTime("2025-05-28"));
        $request->setReceiptFile(null);
        $request->setComment(Null);
        $request->setAnswerComment('A très bientôt !');
        $request->setAnswer(true);
        $request->setAnswerAt(new \DateTime());
        $manager->persist($request);
        $this->addReference('request1', $request);

        $request = new Request();
        $request->setRequestType($this->getReference("Congé", RequestType::class));
        $request->setCollaborator($this->getReference('John', Person::class));
        $request->setDepartment($this->getReference('Symfony', Department::class));
        $request->setCreatedAtValue();
        $request->setStartAt(new \DateTime("2025-06-12"));
        $request->setEndAt(new \DateTime("2025-06-25"));
        $request->setReceiptFile(null);
        $request->setComment(NULL);
        $request->setAnswerComment(NULL);
        $request->setAnswer(true);
        $request->setAnswerAt(new \DateTime());
        $manager->persist($request);
        $this->addReference('request2', $request);

        $request = new Request();
        $request->setRequestType($this->getReference("Congé maternité", RequestType::class));
        $request->setCollaborator($this->getReference('Jane', Person::class));
        $request->setDepartment($this->getReference('CMS', Department::class));
        $request->setCreatedAtValue();
        $request->setStartAt(new \DateTime('2025-03-01'));
        $request->setEndAt(new \DateTime("2025-06-03"));
        $request->setReceiptFile('MotDuDocteur.pdf');
        $request->setComment('Je pars en congé maternité.');
        $request->setAnswerComment('Félicitations ! Bon repos et bon courage !');
        $request->setAnswer(false);
        $request->setAnswerAt(new \DateTime('0000-00-00'));
        $manager->persist($request);
        $this->addReference('request3', $request);

        $request = new Request();
        $request->setRequestType($this->getReference("Congé paternité", RequestType::class));
        $request->setCollaborator($this->getReference('Charlie', Person::class));
        $request->setDepartment($this->getReference('BU Design', Department::class));
        $request->setCreatedAtValue();
        $request->setStartAt(new \DateTime("2025-08-15"));
        $request->setEndAt(new \DateTime("2025-09-03"));
        $request->setReceiptFile('Acte2Naissance.pdf');
        $request->setComment('En tant que père, je pars accomplir mon devoir !');
        $request->setAnswerComment(null);
        $request->setAnswer(false);
        $request->setAnswerAt(new \DateTime('0000-00-00'));
        $manager->persist($request);
        $this->addReference('request4', $request);

        $request = new Request();
        $request->setRequestType($this->getReference("Congé maladie", RequestType::class));
        $request->setCollaborator($this->getReference('Alice', Person::class));
        $request->setDepartment($this->getReference('Marketing', Department::class));
        $request->setCreatedAtValue();
        $request->setStartAt(new \DateTime("2025-11-01"));
        $request->setEndAt(new \DateTime("2025-12-31"));
        $request->setReceiptFile('Ordonnance.png');
        $request->setComment('Je suis malade.');
        $request->setAnswerComment(NULL);
        $request->setAnswer(false);
        $request->setAnswerAt(new \DateTime('0000-00-00'));
        $manager->persist($request);
        $this->addReference('request5', $request);

        $request = new Request();
        $request->setRequestType($this->getReference("Congé maladie", RequestType::class));
        $request->setCollaborator($this->getReference('Bob', Person::class));
        $request->setDepartment($this->getReference('Marketing', Department::class));
        $request->setCreatedAtValue();
        $request->setStartAt(new \DateTime("2025-07-01"));
        $request->setEndAt(new \DateTime("2025-07-15"));
        $request->setReceiptFile(null);
        $request->setComment('Vacances d\'été.');
        $request->setAnswerComment('Bonnes vacances !');
        $request->setAnswer(true);
        $request->setAnswerAt(new \DateTime('2025-06-01'));
        $manager->persist($request);
        $this->addReference('request6', $request);

        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [
            RequestTypeFixtures::class,
            DepartmentFixtures::class
        ];
    }
}