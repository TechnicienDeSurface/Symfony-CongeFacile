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
        $request->setStartAt(new \DateTime());
        $request->setEndAt(new \DateTime("2025-05-28"));
        $request->setReceiptFile('receiptFile');
        $request->setComment('comment');
        $request->setAnswerComment('answerComment');
        $request->setAnswer(true);
        $request->setAnswerAt(new \DateTime());
        $manager->persist($request);
        $this->addReference('request1', $request);

        $request = new Request();
        $request->setRequestType($this->getReference("Congé", RequestType::class));
        $request->setCollaborator($this->getReference('John', Person::class));
        $request->setDepartment($this->getReference('Symfony', Department::class));
        $request->setCreatedAtValue();
        $request->setStartAt(new \DateTime());
        $request->setEndAt(new \DateTime("2025-06-25"));
        $request->setReceiptFile('receiptFile');
        $request->setComment('comment');
        $request->setAnswerComment('answerComment');
        $request->setAnswer(true);
        $request->setAnswerAt(new \DateTime());
        $manager->persist($request);
        $this->addReference('request2', $request);

        $request = new Request();
        $request->setRequestType($this->getReference("Congé maternité", RequestType::class));
        $request->setCollaborator($this->getReference('Jane', Person::class));
        $request->setDepartment($this->getReference('CMS', Department::class));
        $request->setCreatedAtValue();
        $request->setStartAt(new \DateTime());
        $request->setEndAt(new \DateTime("2025-06-03"));
        $request->setReceiptFile('receiptFile');
        $request->setComment('comment');
        $request->setAnswerComment('answerComment');
        $request->setAnswer(true);
        $request->setAnswerAt(new \DateTime());
        $manager->persist($request);
        $this->addReference('request3', $request);

        $request = new Request();
        $request->setRequestType($this->getReference("Congé paternité", RequestType::class));
        $request->setCollaborator($this->getReference('Charlie', Person::class));
        $request->setDepartment($this->getReference('BU Design', Department::class));
        $request->setCreatedAtValue();
        $request->setStartAt(new \DateTime());
        $request->setEndAt(new \DateTime("2025-09-03"));
        $request->setReceiptFile('receiptFile');
        $request->setComment('comment');
        $request->setAnswerComment('answerComment');
        $request->setAnswer(true);
        $request->setAnswerAt(new \DateTime());
        $manager->persist($request);
        $this->addReference('request4', $request);

        $request = new Request();
        $request->setRequestType($this->getReference("Congé maladie", RequestType::class));
        $request->setCollaborator($this->getReference('Alice', Person::class));
        $request->setDepartment($this->getReference('Marketing', Department::class));
        $request->setCreatedAtValue();
        $request->setStartAt(new \DateTime());
        $request->setEndAt(new \DateTime("2025-12-31"));
        $request->setReceiptFile('receiptFile');
        $request->setComment('comment');
        $request->setAnswerComment('answerComment');
        $request->setAnswer(true);
        $request->setAnswerAt(new \DateTime());
        $manager->persist($request);
        $this->addReference('request5', $request);

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