<?php

namespace App\Repository;

use App\Entity\Request;
use Doctrine\ORM\Query;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
/**
 * @extends ServiceEntityRepository<Request>
 */
class RequestRepository extends ServiceEntityRepository
{
    private $entityManager;
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Request::class);
        $this->entityManager = $entityManager;
    }


    /**
     * @return Request[] Returns an array of Request objects
     */
    public function findByCongeByCollaborator($value): array
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(p.id)')
            ->andWhere('r.collaborator = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function getNbConge(): array
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            // ->groupBy('r.collaborator_id')
            ->getQuery()
            ->getResult();
    }

    public function getRequestByPerson($id): ?array
    {
        return $this->createQueryBuilder('r')
            ->join('r.collaborator', 'c')
            ->andWhere('c.id = :id')    
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }
    

    public function getWorkingDays(\DateTime $startAt, \DateTime $endAt)
    {
        $workingDays = 0;
        $currentDate = clone $startAt;

        // Liste des jours fériés fixes en France (format 'd-m')
        $holidays = [
            '01-01', // Jour de l'An
            '01-05', // Fête du Travail
            '08-05', // Victoire 1945
            '14-07', // Fête Nationale
            '15-08', // Assomption
            '01-11', // Toussaint
            '11-11', // Armistice 1918
            '25-12', // Noël
        ];

        // Assure que la date de début est avant la date de fin
        if ($startAt > $endAt) {
            throw new \InvalidArgumentException('La date de début ne peut pas être après la date de fin.');
        }

        // Boucle à travers chaque jour entre les deux dates
        while ($currentDate <= $endAt) {
            // Vérifier si le jour est un jour ouvré (lundi à vendredi) et s'il n'est pas un jour férié
            if ($currentDate->format('N') < 6) { // 'N' retourne 1 pour lundi et 5 pour vendredi
                $formattedDate = $currentDate->format('d-m');
                if (!in_array($formattedDate, $holidays)) {
                    $workingDays++;
                }
            }

            // Passer au jour suivant
            $currentDate->add(new \DateInterval('P1D'));
        }

        return $workingDays;
    }

    public function searchRequest(array $filters, string $order = '')
    {
        $qb = $this->createQueryBuilder('r');
        $qb->leftJoin('r.collaborator', 'collaborator');
        $qb->leftJoin('r.request_type', 'request_type');

        if (!empty($filters['collaborator'])) {
            $qb->andWhere('collaborator.id = :collaboratorId')
                ->setParameter('collaboratorId', $filters['collaborator']->getId());
        
            $qb->andWhere('collaborator.last_name LIKE :last_name')
                ->setParameter('last_name', '%' . $filters['collaborator']->getLastName() . '%');
        
            $qb->orWhere('collaborator.first_name LIKE :first_name')
                ->setParameter('first_name', '%' . $filters['collaborator']->getFirstName() . '%');
        }

        if (!empty($filters['start_at']) && !empty($filters['end_at'])) {
            // Si les deux dates sont renseignées => filtre entre les deux dates
            $qb->andWhere('r.start_at BETWEEN :start_at AND :end_at')
                ->setParameter('start_at', $filters['start_at'])
                ->setParameter('end_at', $filters['end_at']);
        } elseif (!empty($filters['start_at'])) {
            // Si seulement start_at est renseignée => start_at >= date donnée
            $qb->andWhere('r.start_at >= :start_at')
                ->setParameter('start_at', $filters['start_at']);
        } elseif (!empty($filters['end_at'])) {
            // Si seulement end_at est renseignée => filtre sur la date de fin exacte
            $qb->andWhere('r.end_at = :end_at')
                ->setParameter('end_at', $filters['end_at']);
        }

        if (!empty($filters['created_at'])) {
            if ($filters['created_at'] instanceof \DateTime) {
                $startOfDay = (clone $filters['created_at'])->setTime(0, 0, 0);

                $qb->andWhere('r.created_at >= :start_date')
                    ->setParameter('start_date', $startOfDay);
            } else {
                $createdAt = \DateTime::createFromFormat('Y-m-d', $filters['created_at']);
                if ($createdAt) {
                    $startOfDay = (clone $createdAt)->setTime(0, 0, 0);

                    $qb->andWhere('r.created_at >= :start_date')
                        ->setParameter('start_date', $startOfDay);
                }
            }
        }

        if (!empty($filters['request_type'])) {
            $qb->andWhere('r.request_type = :request_type')
               ->setParameter('request_type', $filters['request_type']);
        }

        if (array_key_exists('answer', $filters)) {
            if ($filters['answer'] === "none") {
                $qb->andWhere('r.answer IS NULL');
            } elseif ($filters['answer'] === true) {
                $qb->andWhere('r.answer = true');
            } elseif ($filters['answer'] === false) {
                $qb->andWhere('r.answer = false');
            }
        }

        if (!empty($order)) {
            $qb->orderBy('r.id', $order);
        }

        return $qb->getQuery();
    }

    public function HistoryRequestfindByFilters(array $filters, string $order): Query
    {
        $qb = $this->createQueryBuilder('r');

        // Jointure avec l'entité Collaborator
        $qb->leftJoin('r.collaborator', 'collaborator');

        // Jointure avec l'entité RequestType
        $qb->leftJoin('r.request_type', 'request_type');

        // FILTRE PAR TYPE DE DEMANDE
        if (!empty($filters['request_type'])) {
            $qb->andWhere('request_type.id = :request_type')
                ->setParameter('request_type', $filters['request_type']->getId());
        }

        // FILTRE PAR COLLABORATEUR
        if (!empty($filters['collaborator'])) {
            $qb->andWhere("CONCAT(collaborator.first_name, ' ', collaborator.last_name) = :collaborator")
                ->setParameter('collaborator', $filters['collaborator']->getFirstNameLastName());
        }

        // FILTRE PAR DATE DE CRÉATION
        if (!empty($filters['created_at'])) {
            if ($filters['created_at'] instanceof \DateTime) {
                $filters['created_at']->setTime(0, 0, 0);
                $endOfDay = clone $filters['created_at'];
                $endOfDay->setTime(23, 59, 59);

                $qb->andWhere('r.created_at BETWEEN :start_date AND :end_date')
                    ->setParameter('start_date', $filters['created_at'])
                    ->setParameter('end_date', $endOfDay);
            } else {
                $createdAt = \DateTime::createFromFormat('Y-m-d', $filters['created_at']);
                if ($createdAt) {
                    $createdAt->setTime(0, 0, 0);
                    $endOfDay = clone $createdAt;
                    $endOfDay->setTime(23, 59, 59);

                    $qb->andWhere('r.created_at BETWEEN :start_date AND :end_date')
                        ->setParameter('start_date', $createdAt)
                        ->setParameter('end_date', $endOfDay);
                }
            }
        }

        // FILTRE PAR DATE DE DÉPART
        if (!empty($filters['start_at'])) {
            if ($filters['start_at'] instanceof \DateTime) {
                $qb->andWhere('r.start_at >= :start_at')
                    ->setParameter('start_at', $filters['start_at']);
            } else {
                $startAt = \DateTime::createFromFormat('Y-m-d', $filters['start_at']);
                if ($startAt) {
                    $qb->andWhere('r.start_at >= :start_at')
                        ->setParameter('start_at', $startAt);
                }
            }
        }

        // FILTRE PAR DATE DE FIN
        if (!empty($filters['end_at'])) {
            if ($filters['end_at'] instanceof \DateTime) {
                $qb->andWhere('r.end_at <= :end_at')
                    ->setParameter('end_at', $filters['end_at']);
            } else {
                $endAt = \DateTime::createFromFormat('Y-m-d', $filters['end_at']);
                if ($endAt) {
                    $qb->andWhere('r.end_at <= :end_at')
                        ->setParameter('end_at', $endAt);
                }
            }
        }

        // FILTRE PAR STATUT
        if (array_key_exists('answer', $filters)) {
            if ($filters['answer'] === "none") {
                $qb->andWhere('r.answer IS NULL');
            }elseif($filters['answer'] === false || $filters['answer'] === true) {
                $qb->andWhere('r.answer = :answer')
                    ->setParameter('answer', $filters['answer']);
            }
        }

        if (!empty($order)) {
            $qb->orderBy('r.id', $order);
        }

        return $qb->getQuery();
    }

    // DEMANDES EN ATTENTES
    /**
     * @var Request[] $requests
     */
    public function findRequestPendingByManager($managerId)
    {
        return $this->createQueryBuilder('r')
            ->select('r','p')
            ->innerJoin('r.collaborator', 'p')
            ->where('p.manager = :managerId')
            ->andWhere('r.answer IS NULL')
            ->setParameter('managerId', $managerId)
            ->getQuery()
            ->getResult();
    }

    // FILTRES DEMANDES EN ATTENTES
    /**
     * @var Request[] $requests
     */
    public function findRequestPendingByFilters($managerId, $filters, $order)
    {
        $qb = $this->createQueryBuilder('r')
            ->select('r', 'p')
            ->innerJoin('r.collaborator', 'p')
            ->where('p.manager = :managerId')
            ->andWhere('r.answer IS NULL')
            ->setParameter('managerId', $managerId);

        // Ajouter des conditions de filtrage dynamiques en fonction des valeurs présentes dans $filters
        if (!empty($filters['request_type'])) {
            $qb->andWhere('r.request_type = :requestType')
            ->setParameter('requestType', $filters['request_type']);
        }

        if (!empty($filters['collaborator'])) {
            $qb->andWhere('p.id = :collaboratorId')
            ->setParameter('collaboratorId', $filters['collaborator']);
        }

        if (!empty($filters['start_at'])) {
            $qb->andWhere('r.start_at >= :startAt')
            ->setParameter('startAt', $filters['start_at']);
        }

        if (!empty($filters['end_at'])) {
            $qb->andWhere('r.end_at <= :endAt')
            ->setParameter('endAt', $filters['end_at']);
        }

        if (!empty($filters['created_at'])) {
            $qb->andWhere('r.created_at >= :createdAt')
            ->setParameter('createdAt', $filters['created_at']);
        }

        // Ajouter un ordre de tri (si applicable)
        if ($order == 'ASC' || $order == 'DESC') {
            $qb->orderBy('r.end_at - r.start_at', $order); // Ou tout autre critère d'ordre
        }

        return $qb->getQuery()->getResult();
    }


    //COMPTEURS DE DEMANDES EN ATTENTES
    public function findCountRequestPendingByManager($managerId): int
    {
        return (int) $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->innerJoin('r.collaborator', 'p')
            ->where('p.manager = :managerId')
            ->andWhere('r.answer IS NULL')
            ->setParameter('managerId', $managerId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    //Trouvé des demandes par un type de demande pour la vérification de suppression de postes
    public function findRequestByTypeOfRequest(int $id): ?int
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->join('r.request_type', 'request_type') 
            ->where('request_type.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleScalarResult(); 
    }




}
