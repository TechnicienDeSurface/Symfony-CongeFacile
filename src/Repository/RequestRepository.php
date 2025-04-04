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
            $qb->leftJoin('request.request_type','request_type');
            if (!empty($filters['collaborator'])) {
                $qb->andWhere('collaborator.last_name LIKE :last_name')
                ->setParameter('last_name', '%' . $filters['collaborator'] . '%'); 
                $qb->orWhere('collaborator.first_name LIKE :first_name')
                ->setParameter('first_name', '%' . $filters['collaborator'] . '%'); 
            }
            if (!empty($filters['start_at']) && !empty($filters['end_at'])) {
                $qb->andWhere('request.start_at BETWEEN :startDate AND :endDate AND request.end_at BETWEEN :startDate AND :endDate')
                    ->setParameter('startDate', $filters['start_at'])
                    ->setParameter('endDate', $filters['end_at']);
            }
            if(!empty($filters['request_type'])){
                $qb->andWhere('request_type.name LIKE :name')
                ->setParameter('name', '%'.$filters['request_type'].'%'); 
            }

            if(!empty($filters['answer'])){
                $qb->andWhere('request.answer LIKE :answer')
                ->setParameter('answer',$filters['answer']); 
            }

            if (!empty($order)) {
                $qb->orderBy('request.id', $order);
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
