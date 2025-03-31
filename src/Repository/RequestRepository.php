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

       public function findByFilters(array $filters): array
       {
           $qb = $this->createQueryBuilder('request');
   
           // Jointure avec l'entité Person
           $qb->leftJoin('request.person', 'person');

           // Jointure avec l'entité Person
           $qb->leftJoin('request.request_type', 'request_type');
   
           // FILTRE PAR TYPE DE DEMANDE
           if (!empty($filters['request_type'])) {
               $qb->andWhere('request.request_type LIKE :request_type')
                  ->setParameter('request_type', '%' . $filters['request_type'] . '%');
            }

            // FILTRE PAR COLLABORATEUR
            if (!empty($filters['collaborator'])) {
                $qb->andWhere('request.collaborator LIKE :collaborator')
                    ->setParameter('collaborator', '%' . $filters['collaborator'] . '%');    
            }
   
           // FILTRE PAR DATE DE DEPART
           if (!empty($filters['start_at'])) {
               $qb->andWhere('request.start_at LIKE :start_at')
                  ->setParameter('start_at', '%' . $filters['start_at'] . '%');
            }

            // FILTRE PAR DATE DE FIN
            if (!empty($filters['end_at'])) {
            $qb->andWhere('request.end_at LIKE :end_at')
                ->setParameter('end_at', '%' . $filters['end_at'] . '%');
            }

            // FILTRE PAR STATUT
            if (!empty($filters['answert'])) {
                $qb->andWhere('request.answert LIKE :answert')
                    ->setParameter('answert', '%' . $filters['answert'] . '%');
                }

   
           return $qb->getQuery()->getResult();
       }
}
