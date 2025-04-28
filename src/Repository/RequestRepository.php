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

    public function searchRequest(array $filters, string $order = ''): Query
    {
        $qb = $this->createQueryBuilder('request');
        $qb->leftJoin('request.collaborator', 'collaborator');
        $qb->leftJoin('request.request_type', 'request_type');

        if (!empty($filters['collaborator'])) {
            $qb->andWhere('collaborator.id = :collaboratorId')
                ->setParameter('collaboratorId', $filters['collaborator']->getId());
        }

        if (!empty($filters['start_at']) && !empty($filters['end_at'])) {
            // Si les deux dates sont renseignées => filtre entre les deux dates
            $qb->andWhere('request.start_at BETWEEN :start_at AND :end_at')
                ->setParameter('start_at', $filters['start_at'])
                ->setParameter('end_at', $filters['end_at']);
        } elseif (!empty($filters['start_at'])) {
            // Si seulement start_at est renseignée => start_at >= date donnée
            $qb->andWhere('request.start_at >= :start_at')
                ->setParameter('start_at', $filters['start_at']);
        } elseif (!empty($filters['end_at'])) {
            // Si seulement end_at est renseignée => filtre sur la date de fin exacte
            $qb->andWhere('request.end_at = :end_at')
                ->setParameter('end_at', $filters['end_at']);
        }

        if (!empty($filters['created_at'])) {
            if ($filters['created_at'] instanceof \DateTime) {
                $startOfDay = (clone $filters['created_at'])->setTime(0, 0, 0);

                $qb->andWhere('request.created_at >= :start_date')
                    ->setParameter('start_date', $startOfDay);
            } else {
                $createdAt = \DateTime::createFromFormat('Y-m-d', $filters['created_at']);
                if ($createdAt) {
                    $startOfDay = (clone $createdAt)->setTime(0, 0, 0);

                    $qb->andWhere('request.created_at >= :start_date')
                        ->setParameter('start_date', $startOfDay);
                }
            }
        }

        if (!empty($filters['request_type'])) {
            $qb->andWhere('request_type = :request_type')
                ->setParameter('request_type', $filters['request_type']);
        }

        if (!empty($filters['answer'])) {
            if ($filters['answer'] === "none") {
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

    public function calculateDayWorking(\DateTime $date1, \DateTime $date2): int
    {
        $joursOuvres = 0;

        // Assure-toi que la date1 est avant ou égale à la date2
        if ($date1 > $date2) {
            return 0;
        }

        // Parcours les jours entre les deux dates
        while ($date1 <= $date2) {
            $jourSemaine = $date1->format('w'); // 0 = dimanche, 6 = samedi
            if ($jourSemaine != 0 && $jourSemaine != 6) { // Exclure le samedi (6) et le dimanche (0)
                $joursOuvres++;
            }
            $date1->modify('+1 day'); // Incrémente la date d'un jour
        }

        return $joursOuvres;
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
