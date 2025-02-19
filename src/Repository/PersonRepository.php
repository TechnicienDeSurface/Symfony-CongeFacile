<?php

namespace App\Repository;

use App\Entity\Person;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Person>
 */
class PersonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Person::class);
    }

       /**
        * @return Person[] Returns an array of Person objects
        */
       public function findByFirstname($value): array
       {
           return $this->createQueryBuilder('p')
               ->andWhere('p.firstname = :val')
               ->setParameter('val', $value)
               ->orderBy('p.id', 'ASC')
               ->setMaxResults(10)
               ->getQuery()
               ->getResult()
           ;
       }

       public function findByLasname($value): array
       {
           return $this->createQueryBuilder('p')
               ->andWhere('p.lastname = :val')
               ->setParameter('val', $value)
               ->orderBy('p.id', 'ASC')
               ->setMaxResults(10)
               ->getQuery()
               ->getResult()
           ;
       }

       public function findByEmail($value): array
       {
           return $this->createQueryBuilder('p')
               ->andWhere('p.email = :val')
               ->setParameter('val', $value)
               ->orderBy('p.id', 'ASC')
               ->setMaxResults(10)
               ->getQuery()
               ->getResult()
           ;
       }

       public function findByFirstPoste($value): array
       {
           return $this->createQueryBuilder('p')
                ->innerJoin('p.position', 'f')
                ->andWhere('p.position.name = :val')
                ->setParameter('val', $value)
                ->orderBy('p.id', 'ASC')
                ->setMaxResults(10)
                ->getQuery()
                ->getResult()
           ;
       }

       public function findByNbConge($value): array
       {
           return $this->createQueryBuilder('p')
               ->andWhere('p.firstname = :val')
               ->setParameter('val', $value)
               ->orderBy('p.id', 'ASC')
               ->setMaxResults(10)
               ->getQuery()
               ->getResult()
           ;
       }

       public function findOneBySomeField($value): ?Person
       {
           return $this->createQueryBuilder('p')
               ->andWhere('p.exampleField = :val')
               ->setParameter('val', $value)
               ->getQuery()
               ->getOneOrNullResult()
           ;
       }

    
}
