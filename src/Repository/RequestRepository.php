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
            ->getResult();
    }

    public function getTeamRequest($id): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.collaborator = :val')
            ->setParameter('val', $id)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function searchRequest(array $filters,  string $order = ''): Query
    {
        $qb = $this->createQueryBuilder('request');
        // Correction : jointure correcte avec l'entité person
        $qb->leftJoin('request.collaborator', 'collaborator');
        $qb->leftJoin('request.request_type', 'request_type');

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

        if (!empty($filters['request_type'])) {
            $qb->andWhere('request_type.name LIKE :name')
                ->setParameter('name', '%' . $filters['request_type'] . '%');
        }

        if (!empty($filters['answer'])) {
            if ($filters['answer'] == "none") {  //condition pour aller chercher les answer null avec la valeur none
                $qb->andWhere('request.answer IS NULL');
            } else {
                $qb->andWhere('request.answer LIKE :answer')
                    ->setParameter('answer', $filters['answer']);
            }
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

        // FILTRE PAR DATE DE CRÉATION
        if (!empty($filters['created_at'])) {
            // INSTANCIATION D'UN OBJET DATETIME
            if ($filters['created_at'] instanceof \DateTime) {
                // Set l'heure à 00:00:00 pour ne pas tenir compte de l'heure
                $filters['created_at']->setTime(0, 0, 0);
                // Définir la fin de la journée à 23:59:59
                $endOfDay = clone $filters['created_at'];
                $endOfDay->setTime(23, 59, 59);

                //FILTRE DÉBUT DE JOURNE
                $qb->andWhere('request.created_at BETWEEN :start_date AND :end_date')
                    ->setParameter('start_date', $filters['created_at'])
                    ->setParameter('end_date', $endOfDay);
            } else {
                $createdAt = \DateTime::createFromFormat('Y-m-d', $filters['created_at']);
                if ($createdAt) {
                    // Set l'heure à 00:00:00 pour éviter de comparer les heures
                    $createdAt->setTime(0, 0, 0);
                    // Définir la fin de la journée à 23:59:59
                    $endOfDay = clone $createdAt;
                    $endOfDay->setTime(23, 59, 59);

                    //FILTRE DÉBUT DE JOURNE
                    $qb->andWhere('request.created_at BETWEEN :start_date AND :end_date')
                        ->setParameter('start_date', $createdAt)
                        ->setParameter('end_date', $endOfDay);
                }
            }
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
