<?php

namespace App\Repository;

use App\Entity\Request;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
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

       public function HistoryRequestfindByFilters(array $filters, string $order = ''): Query
       {
           $qb = $this->createQueryBuilder('request');
   
           // Jointure avec l'entité Person
           $qb->leftJoin('request.collaborator', 'person');

           // Jointure avec l'entité Person
           $qb->leftJoin('request.request_type', 'request_type');

           //$qb->addSelect('DATEDIFF(request.end_at, request.start_at) AS nbdays');
   
           // FILTRE PAR TYPE DE DEMANDE
           if (!empty($filters['request_type'])) {
                // Ici, comme tu as un objet, on filtre par ID
                $qb->andWhere('request_type.id = :request_type')
                ->setParameter('request_type', $filters['request_type']->getId());
            }

            // FILTRE PAR COLLABORATEUR
            if (!empty($filters['collaborator'])) {
                $qb->andWhere("CONCAT(person.first_name, ' ', person.last_name) = :collaborator")
                   ->setParameter('collaborator', $filters['collaborator']->getFirstNameLastName());
            }
   
            // FILTRE PAR DATE DE DEPART
            if (!empty($filters['start_at'])) {
                //INSTANCIATION D'UN OBJET DATETIME
                if ($filters['start_at'] instanceof \DateTime) {
                    $qb->andWhere('request.start_at >= :start_at')
                        ->setParameter('start_at', $filters['start_at']);
                } else {
                    $startAt = \DateTime::createFromFormat('Y-m-d', $filters['start_at']);
                    if ($startAt) {
                        $qb->andWhere('request.start_at >= :start_at')
                            ->setParameter('start_at', $startAt);
                    }
                }
            }

            // FILTRE PAR DATE DE FIN
            if (!empty($filters['end_at'])) {
                if ($filters['end_at'] instanceof \DateTime) {
                    $qb->andWhere('request.end_at <= :end_at')
                        ->setParameter('end_at', $filters['end_at']);
                } else {
                    $endAt = \DateTime::createFromFormat('Y-m-d', $filters['end_at']);
                    if ($endAt) {
                        $qb->andWhere('request.end_at <= :end_at')
                            ->setParameter('end_at', $endAt);
                    }
                }
            }

            //FILTRE PAR NOMBRE DE JOURS
            if (!empty($filters['nbdays'])) {
                $order = $filters['nbdays'] === 'ASC' ? 'ASC' : 'DESC';
                $qb->orderBy('nbdays', $order); // Trier par le calcul des jours
            }

            // FILTRE PAR STATUT
            if (array_key_exists('answer', $filters)) {
                if ($filters['answer'] === null) {
                    $qb->andWhere('request.answer IS NULL');
                } else {
                    $qb->andWhere('request.answer = :answer')
                       ->setParameter('answer', $filters['answer']);
                }
            }

   
           return $qb->getQuery();
       }
}
