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
        $request->setRequestType($this->getReference("Collaborateur", RequestType::class));
        $request->setCollaborator($this->getReference('John', Person::class));
        $request->setDepartment($this->getReference('Symfony', Department::class));
        $request->setCreatedAtValue();
        $request->setStartAt(new \DateTime());
        $request->setEndAt(new \DateTime());
        $request->setReceiptFile('receiptFile');
        $request->setComment('comment');
        $request->setAnswerComment('answerComment');
        $request->setAnswer(true);
        $request->setAnswerAt(new \DateTime());
        $manager->persist($request);
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