<?php

namespace App\Repository;

use App\Entity\Request;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Request>
 */
class RequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Request::class);
    }

       /**
        * @return Request[] Returns an array of Request objects
        */
       public function findByCongeByCollaborator($value): array
       {
           return $this->createQueryBuilder('r')
                ->select('COUNT(p.id)')
               ->andWhere('r.collaborator_id = :val')
               ->setParameter('val', $value)
               ->orderBy('r.id', 'ASC')
               ->setMaxResults(10)
               ->getQuery()
               ->getResult()
           ;
       }

       public function getNbConge(): array
       {
        return $this->createQueryBuilder('r')
                ->select('COUNT(r.id)')
                // ->groupBy('r.collaborator_id')
                ->getQuery()
                ->getResult() ; 
       }

    //    public function findOneBySomeField($value): ?Request
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
