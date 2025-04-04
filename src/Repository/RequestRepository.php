<?php

namespace App\Repository;

use App\Entity\Request;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

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

       public function searchTypeOfRequest(array $filters,  string $order = ''): Query
    {
        $qb = $this->createQueryBuilder('request');
    
        // Correction : jointure correcte avec l'entité person
        $qb->leftJoin('request.collaborator', 'collaborator');
    
        if (!empty($filters['collaborator'])) {
            $qb->andWhere('collaborator.last_name LIKE :last_name')
               ->setParameter('last_name', '%' . $filters['collaborator'] . '%');
        }
        if(!empty($filters['start_at']) && !empty($filters['end_at'])){
            $qb->where('e.start_at BETWEEN :startDate AND :endDate AND e.end_at BETWEEN :startDate AND :endDate ')
            ->setParameter('startDate', $filters['star_at'])
            ->setParameter('endDate', $filters['end_at']); 

        }

        if ($order) {
            $qb->orderBy('request.*', $order);
        }
    
        return $qb->getQuery();
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
